import pandas as pd
import numpy as np
import json
import sys
import pickle

# Load the Agglomerative model and scaler
with open('./models/binary_models/agglomerative_clustering_model.pkl', 'rb') as model_file:
    agg_model, scaler, _ = pickle.load(model_file)

# Define column names
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

# Read JSON input
input_data = json.load(sys.stdin)

# Extract and encode
age = input_data['age']
sexe = input_data['sexe']
nom_vaccin = input_data['nom_vaccin']
lieu_vaccination = input_data['lieu_vaccination']
saison_vaccination = input_data['saison_vaccination']
medical_history = input_data['medical_history'].split(',')

sexe_vector = [1 if nv == sexe else 0 for nv in sexe_columns]
nom_vaccin_vector = [1 if nv == nom_vaccin else 0 for nv in nom_vaccin_columns]
lieu_vaccination_vector = [1 if lv == lieu_vaccination else 0 for lv in lieu_vaccination_columns]
saison_vaccination_vector = [1 if sv == saison_vaccination else 0 for sv in saison_vaccination_columns]
medical_history_binary = [1 if mh in medical_history else 0 for mh in medical_history_columns]
side_effects_vector = [0] * len(side_effects_columns)

# Combine features
input_vector = [age] + sexe_vector + nom_vaccin_vector + lieu_vaccination_vector + saison_vaccination_vector + medical_history_binary + side_effects_vector
input_vector = np.array(input_vector).reshape(1, -1)

# Scale and predict
input_vector_scaled = scaler.transform(input_vector)
predicted_cluster = agg_model.fit_predict(input_vector_scaled)[0]

# Define side effects per cluster (dummy values to be replaced by your analysis)
cluster_side_effects = {
    0: {'Fievre': 100, 'Fatigue': 80},
    1: {'Fievre': 50, 'Fatigue': 40}
}

predicted_side_effects = cluster_side_effects.get(predicted_cluster, {})

# Output result
print(json.dumps(predicted_side_effects))
