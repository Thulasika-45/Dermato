from flask import Flask, request, jsonify
from werkzeug.utils import secure_filename
import os
import numpy as np
from tensorflow.keras.preprocessing import image
from tensorflow.keras.models import load_model
from datetime import datetime
import mysql.connector

app = Flask(__name__)
UPLOAD_FOLDER = 'uploads'
if not os.path.exists(UPLOAD_FOLDER):
    os.makedirs(UPLOAD_FOLDER)

# ✅ Load the model
model = load_model('./models/Skin_Disease_Classifier.h5')

# Class labels with descriptions
class_labels = {
     0: ("Acne", "A common skin condition causing pimples, blackheads, and cysts.", 
        " Can be treated with topical treatments, oral medications, and proper skincare."),
    1: ("Actinic Cheilitis", "A precancerous condition affecting the lips due to sun damage.", 
        " Early treatment includes sun protection and topical medications."),
    2: ("Actinic Keratosis", "A rough, scaly patch on the skin caused by sun exposure.", 
        " May require cryotherapy, topical creams, or laser therapy."),
    3: ("Atopic Dermatitis", "A chronic skin condition causing dry, itchy, and inflamed skin.", 
        " Managed with moisturizers, antihistamines, and steroid creams."),
    4: ("Drug Eruptions", "Skin reactions caused by medications.", 
        " Treatment involves discontinuing the drug and using anti-inflammatory medications."),
    5: ("Eczema", "A condition causing dry, itchy, and inflamed skin patches.", 
        " Managed with moisturizers, steroids, and lifestyle changes."),
    6: ("Exanthems", "Widespread rashes caused by viral or bacterial infections.", 
        " Usually self-limiting but may require symptomatic treatment."),
    7: ("Herpes", "A viral infection causing painful blisters.", 
        "Treated with antiviral medications."),
    8: ("Melanoma", "A serious type of skin cancer originating in pigment cells.", 
        " Early detection is critical; may require surgery, chemotherapy, or immunotherapy."),
    9: ("Pemphigus", "A rare autoimmune disorder causing blistering skin lesions.", 
        " Managed with immunosuppressive drugs."),
    10: ("Psoriasis", "An autoimmune disease causing red, scaly skin patches.", 
        " Managed with topical treatments, biologics, and light therapy."),
    11: ("Rosacea", "A chronic skin condition causing facial redness and swelling.", 
        " Managed with topical and oral medications."),
    12: ("Sun-Damaged Skin", "Skin changes due to prolonged sun exposure.", 
        " Preventive measures include sunscreen and antioxidant skincare."),
    13: ("Tinea (Ringworm) / Candidiasis", "Fungal infections causing itchy, ring-shaped rashes.", 
        " Treated with antifungal creams or oral antifungal medications."),
    14: ("Vitiligo", "A condition causing loss of skin pigment in patches.", 
        " Managed with phototherapy and topical treatments."),
    15: ("Warts", "Small, rough growths caused by HPV.", 
        " Treated with cryotherapy, salicylic acid, or laser treatment."),
}

# ✅ Image preprocessing
def preprocess_image(img_path, target_size=(224, 224)):
    img = image.load_img(img_path, target_size=target_size)
    img_array = image.img_to_array(img)
    img_array = np.expand_dims(img_array, axis=0)
    img_array /= 255.0
    return img_array

# ✅ Diagnose endpoint
@app.route('/diagnose', methods=['POST'])
def diagnose():
    if 'file' not in request.files:
        return jsonify({"error": "No file uploaded"}), 400

    file = request.files['file']
    if file.filename == '':
        return jsonify({"error": "No selected file"}), 400

    filename = secure_filename(file.filename)
    filepath = os.path.join(UPLOAD_FOLDER, filename)
    file.save(filepath)

    img_array = preprocess_image(filepath)
    predictions = model.predict(img_array)
    predicted_index = int(np.argmax(predictions))
    disease, description, recommendation = class_labels.get(predicted_index, ("Unknown", "No description available", "No recommendation"))

    return jsonify({
        "disease": disease,
        "description": description,
        "recommendation": recommendation
    })

if __name__ == '__main__':
    app.run(debug=True, port=5000)
