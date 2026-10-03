import json
import numpy as np
import pickle
import sys

# Load the model, scaler, and label encoders
with open('./predictions/Models/gmm_model.pkl', 'rb') as model_file:
    gmm, scaler, label_encoders = pickle.load(model_file)

# Define the medical history and side effects columns
medical_history_columns = [
    'Anemie', 'Arthrose', 'Allergie', 'Bronchopneumopathie chronique obstructive (BPCO)', 
    'Cardiaque', 'Colopathie', 'Diphtérie-Tétanos', 'Diabete Type 1', 'Diabete Type 2', 
    'Dyslipidémie', 'Déficit en Proteine', 'Gastrite', 'Hypertension artérielle (HTA)', 
    'Hemiplégie', 'Hypothyroide Glucome', 'Hypothyroidie', 'Hypovitaminose B12', 'Insuffisance Rénale', 
    'No Background', 'Rectocolite hémorragique (RCUH)', 'Rhinite Allergique', 'Thyroidie', 
    'Accident Vasculaire Cérébral (AVC)', 'Varices'
]

# Define the side effects columns
side_effects_columns = [
    'Douleurs musculaires', 'Diarhée', 'Fatigue', 'Fievre', 'Frisson', 'Insomnie', 
    'Maux de tete', 'Nausées', 'Neveralgie', 'Palpitation', 'Toux', 'Troubles Digestifs', 
    'Vertige', 'Allergie Cutanée', 'Briefées Sectaleuse', 'Ecoulement', 'Méningite', 
    'Métrorragie', 'Nounissement', 'Syndrom Grippal'
]

# Read JSON data from stdin
input_data = json.load(sys.stdin)

# Extract input data
age = input_data['age']
sexe = input_data['sexe']
nom_vaccin = input_data['nom_vaccin']
lieu_vaccination = input_data['lieu_vaccination']
saison_vaccination = input_data['saison_vaccination']
medical_history = input_data['medical_history'].split(',')

# Encode categorical variables
sexe_encoded = label_encoders['Sexe'].transform([sexe])[0]
nom_vaccin_encoded = label_encoders['Nom Vaccin'].transform([nom_vaccin])[0]
lieu_vaccination_encoded = label_encoders['Lieu de La vaccination'].transform([lieu_vaccination])[0]
saison_vaccination_encoded = label_encoders['Saison de Vaccination'].transform([saison_vaccination])[0]

# Prepare medical history binary vector
medical_history_binary = [0] * len(medical_history_columns)
for history in medical_history:
    if history in medical_history_columns:
        medical_history_binary[medical_history_columns.index(history)] = 1

# Create the input vector for prediction
input_vector = [age, sexe_encoded, nom_vaccin_encoded, lieu_vaccination_encoded, saison_vaccination_encoded] + medical_history_binary
input_vector = np.array(input_vector).reshape(1, -1)

# Scale the input data
input_vector_scaled = scaler.transform(input_vector)

# Predict the cluster
predicted_cluster = gmm.predict(input_vector_scaled)[0]

# Define the side effects for each cluster
cluster_side_effects = {
    0: {
        'Fievre': 37,
        'Douleurs musculaires': 31,
        'Fatigue': 19,
        'Frisson': 15,
        'Maux de tete': 13,
        'Diarhée': 3,
        'Vertige': 3,
        'Nausées': 2,
        'Neveralgie': 1,
        'Insomnie': 0,
        'Palpitation': 0,
        'Toux': 0,
        'Troubles Digestifs': 0,
        'Allergie Cutanée': 0,
        'Briefées Sectaleuse': 0,
        'Ecoulement': 0,
        'Méningite': 0,
        'Métrorragie': 0,
        'Nounissement': 0,
        'Syndrom Grippal': 0
    },
    1: {
        'Fievre': 147,
        'Fatigue': 108,
        'Douleurs musculaires': 100,
        'Maux de tete': 47,
        'Frisson': 29,
        'Nausées': 7,
        'Vertige': 6,
        'Diarhée': 5,
        'Syndrom Grippal': 2,
        'Insomnie': 1,
        'Palpitation': 1,
        'Toux': 1,
        'Troubles Digestifs': 1,
        'Allergie Cutanée': 1,
        'Briefées Sectaleuse': 1,
        'Ecoulement': 1,
        'Méningite': 1,
        'Métrorragie': 1,
        'Nounissement': 1,
        'Neveralgie': 0
    },
}

# Get the predicted side effects for the cluster
predicted_side_effects = cluster_side_effects[predicted_cluster]

# Output the predicted side effects as JSON
print(json.dumps(predicted_side_effects))
