import json
import numpy as np
import pickle
import sys
from scipy.spatial.distance import cdist

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

# Load the model, scaler, and label encoders
with open('./predictions/Models/agg_clustering_model.pkl', 'rb') as model_file:
    agglomerative_model, scaler, label_encoders, cluster_centers = pickle.load(model_file)

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

# Predict the cluster by finding the nearest cluster center
distances = cdist(input_vector_scaled, cluster_centers)
predicted_cluster = np.argmin(distances)

cluster_side_effects = {
    0: {
        'Fievre': 22,
        'Douleurs musculaires': 17,
        'Fatigue': 17,
        'Frisson': 9,
        'Maux de tete': 8,
        'Diarhée': 2,
        'Vertige': 1,
        'Insomnie': 0,
        'Nausées': 0,
        'Neveralgie': 0,
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
        'Fievre': 145,
        'Fatigue': 107,
        'Douleurs musculaires': 98,
        'Maux de tete': 48,
        'Frisson': 29,
        'Nausées': 7,
        'Vertige': 6,
        'Diarhée': 4,
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
    2: {
        'Douleurs musculaires': 3,
        'Fievre': 3,
        'Diarhée': 1,
        'Fatigue': 1,
        'Maux de tete': 1,
        'Frisson': 0,
        'Insomnie': 0,
        'Nausées': 0,
        'Neveralgie': 0,
        'Palpitation': 0,
        'Toux': 0,
        'Troubles Digestifs': 0,
        'Vertige': 0,
        'Allergie Cutanée': 0,
        'Briefées Sectaleuse': 0,
        'Ecoulement': 0,
        'Méningite': 0,
        'Métrorragie': 0,
        'Nounissement': 0,
        'Syndrom Grippal': 0
    },
    3: {
        'Douleurs musculaires': 1,
        'Fievre': 1,
        'Maux de tete': 1,
        'Neveralgie': 1,
        'Diarhée': 0,
        'Fatigue': 0,
        'Frisson': 0,
        'Insomnie': 0,
        'Nausées': 0,
        'Palpitation': 0,
        'Toux': 0,
        'Troubles Digestifs': 0,
        'Vertige': 0,
        'Allergie Cutanée': 0,
        'Briefées Sectaleuse': 0,
        'Ecoulement': 0,
        'Méningite': 0,
        'Métrorragie': 0,
        'Nounissement': 0,
        'Syndrom Grippal': 0
    },
    4: {
        'Douleurs musculaires': 5,
        'Fievre': 4,
        'Frisson': 2,
        'Maux de tete': 2,
        'Nausées': 1,
        'Diarhée': 0,
        'Fatigue': 0,
        'Insomnie': 0,
        'Neveralgie': 0,
        'Palpitation': 0,
        'Toux': 0,
        'Troubles Digestifs': 0,
        'Vertige': 0,
        'Allergie Cutanée': 0,
        'Briefées Sectaleuse': 0,
        'Ecoulement': 0,
        'Méningite': 0,
        'Métrorragie': 0,
        'Nounissement': 0,
        'Syndrom Grippal': 0
    },
    5: {
        'Fievre': 1,
        'Frisson': 1,
        'Vertige': 1,
        'Douleurs musculaires': 0,
        'Diarhée': 0,
        'Fatigue': 0,
        'Insomnie': 0,
        'Maux de tete': 0,
        'Nausées': 0,
        'Neveralgie': 0,
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
    6: {
        'Fievre': 8,
        'Douleurs musculaires': 7,
        'Frisson': 3,
        'Fatigue': 2,
        'Diarhée': 1,
        'Nausées': 1,
        'Vertige': 1,
        'Insomnie': 0,
        'Maux de tete': 0,
        'Neveralgie': 0,
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
    }
}

# Get the predicted side effects for the cluster
predicted_side_effects = cluster_side_effects[predicted_cluster]

# Output the predicted side effects as JSON
print(json.dumps(predicted_side_effects))
