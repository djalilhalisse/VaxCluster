import pandas as pd
from sklearn.cluster import KMeans
from sklearn.preprocessing import StandardScaler
import json
import sys
import numpy as np
import pickle

# Load the model and scaler
with open('./models/binary_models/kmeans_model.pkl', 'rb') as model_file:
    kmeans, scaler = pickle.load(model_file)

# Define column names for categorical variables
sexe_columns = ['Male', 'Female']
nom_vaccin_columns = ['Sputnik-v-', 'Astra Zeneca', 'Sinopharm', 'Sinovac']
lieu_vaccination_columns = [
    'EPSP Annaba', 'MTV', 'Batimetal', 'Ibn Nafiss', 'SGA', 'SAIDAL',
    'Clinique El Jazair', 'LTPEst', 'BADR', 'Clinique El Farabi', 
    'Dividus', 'SIPA', 'Service MDT', 'Siege'
]
saison_vaccination_columns = ['Summer', 'Spring', 'Winter', 'Autumn']
medical_history_columns = [
    'Anemie', 'Arthrose', 'Allergie', 'Bronchopneumopathie chronique obstructive (BPCO)', 
    'Cardiaque', 'Colopathie', 'Diphtérie-Tétanos', 'Diabete Type 1', 'Diabete Type 2', 
    'Dyslipidémie', 'Déficit en Proteine', 'Gastrite', 'Hypertension artérielle (HTA)', 
    'Hemiplégie', 'Hypothyroide Glucome', 'Hypothyroidie', 'Hypovitaminose B12', 'Insuffisance Rénale', 
    'No Background', 'Rectocolite hémorragique (RCUH)', 'Rhinite Allergique', 'Thyroidie', 
    'Accident Vasculaire Cérébral (AVC)', 'Varices'
]
side_effects_columns = [
    'Fievre', 'Douleurs musculaires', 'Fatigue', 'Frisson', 'Maux de tete', 'Diarhée',
    'Vertige', 'Nausées', 'Neveralgie', 'Insomnie', 'Palpitation', 'Toux', 'Troubles Digestifs',
    'Allergie Cutanée', 'Briefées Sectaleuse', 'Ecoulement', 'Méningite', 'Métrorragie', 
    'Nounissement', 'Syndrom Grippal'
]

# Read JSON data from stdin (assumed to be provided externally)
input_data = json.load(sys.stdin)

# Extract input data
age = input_data['age']
sexe = input_data['sexe']  # Assuming sexe is already 0 or 1
nom_vaccin = input_data['nom_vaccin']
lieu_vaccination = input_data['lieu_vaccination']
saison_vaccination = input_data['saison_vaccination']
medical_history = input_data['medical_history'].split(',')

# Encode categorical variables into binary vectors
sexe_vector = [1 if nv == sexe else 0 for nv in sexe_columns]
nom_vaccin_vector = [1 if nv == nom_vaccin else 0 for nv in nom_vaccin_columns]
lieu_vaccination_vector = [1 if lv == lieu_vaccination else 0 for lv in lieu_vaccination_columns]
saison_vaccination_vector = [1 if sv == saison_vaccination else 0 for sv in saison_vaccination_columns]

# Prepare binary vector for medical history
medical_history_binary = [1 if mh in medical_history else 0 for mh in medical_history_columns]

# Dummy side effects vector (all zeros)
side_effects_vector = [0] * len(side_effects_columns)

# Combine all input features into a single vector
input_vector = [age] + sexe_vector + nom_vaccin_vector + lieu_vaccination_vector + saison_vaccination_vector + medical_history_binary + side_effects_vector
input_vector = np.array(input_vector).reshape(1, -1)

# Scale the age feature only
input_vector_scaled = scaler.transform(input_vector)

# Predict the cluster using the pre-trained K-means model
predicted_cluster = kmeans.predict(input_vector_scaled)[0]

# Define potential side effects for each cluster
cluster_side_effects = {
    0: {
        'Fievre': 147,
        'Fatigue': 108,
        'Douleurs musculaires': 98,
        'Maux de tete': 47,
        'Frisson': 29,
        'Nausées': 7,
        'Vertige': 6,
        'Diarhée': 5,
        'Syndrom Grippal': 2,
        'Palpitation': 1,
        'Troubles Digestifs': 1,
        'Insomnie': 1,
        'Allergie Cutanée': 1,
        'Briefées Sectaleuse': 1,
        'Ecoulement': 1,
        'Méningite': 1,
        'Métrorragie': 1,
        'Nounissement': 1,
        'Toux': 1,
        'Neveralgie': 0
    },
    1: {
        'Fievre': 37,
        'Douleurs musculaires': 33,
        'Fatigue': 19,
        'Frisson': 15,
        'Maux de tete': 13,
        'Diarhée': 3,
        'Vertige': 3,
        'Nausées': 2,
        'Neveralgie': 1,
        'Briefées Sectaleuse': 0,
        'Nounissement': 0,
        'Métrorragie': 0,
        'Méningite': 0,
        'Ecoulement': 0,
        'Toux': 0,
        'Allergie Cutanée': 0,
        'Troubles Digestifs': 0,
        'Palpitation': 0,
        'Insomnie': 0,
        'Syndrom Grippal': 0
    }
}

# Retrieve predicted side effects based on the predicted cluster
predicted_side_effects = cluster_side_effects[predicted_cluster]

# Output the predicted side effects as JSON
print(json.dumps(predicted_side_effects))