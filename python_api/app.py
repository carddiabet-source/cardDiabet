import pandas as pd
import numpy as np
import matplotlib.pyplot as plt
import json
import os
import sys
from warnings import filterwarnings

filterwarnings('ignore')

# --- Diretórios ---
BASE_DIR = os.path.dirname(os.path.abspath(__file__))
public_dir = os.path.join(BASE_DIR, "..", "public", "python_api")
public_dir = os.path.abspath(public_dir)

os.makedirs(public_dir, exist_ok=True)

# ------------------------------
# CARREGAR DADOS (CSV ou JSON)
# ------------------------------
def carregar_dados():
    """Carrega dados de um arquivo CSV ou JSON passado como argumento de linha de comando."""
    if len(sys.argv) < 2:
        sys.exit("Erro: Nenhum dado de entrada (caminho do CSV ou JSON) foi fornecido.")

    input_arg = sys.argv[1]

    if not os.path.exists(input_arg):
        sys.exit(f"Erro: O arquivo de entrada não foi encontrado em '{input_arg}'.")

    if input_arg.lower().endswith('.csv'):
        return pd.read_csv(input_arg, sep=',', dtype=str)
    elif input_arg.lower().endswith('.json'):
        with open(input_arg, 'r', encoding='utf-8') as f:
            data = json.load(f)
        return pd.json_normalize(data, 'historico_consultas', sep='_')
    else:
        sys.exit("Erro: Formato de arquivo não suportado. Use .csv ou .json.")

    return df

df = carregar_dados()

# ------------------------------
# TRATAMENTO DE DATAS E IDADE
# ------------------------------
def tratar_dados(df):
    """Converte tipos de dados, calcula idade e limpa valores nulos."""
    # ------------------------------
    # CONVERSÃO DE DATAS E CÁLCULO DE IDADE
    # ------------------------------
    if 'data_nascimento' in df.columns and 'data_consulta' in df.columns:
        df['data_nascimento'] = pd.to_datetime(df['data_nascimento'], errors='coerce')
        df['data_consulta'] = pd.to_datetime(df['data_consulta'], errors='coerce')
        if not df['data_nascimento'].isnull().all() and not df['data_consulta'].isnull().all():
            df['idade'] = ((df['data_consulta'] - df['data_nascimento']).dt.days / 365.25).fillna(0).astype(int)

    # ------------------------------
    # CONVERSÃO DE NUMÉRICOS
    # ------------------------------
    numericas = [
        'imc', 'pressao_sistolica', 'frequencia_cardiaca_fetal',
        'idade_gestacional', 'peso_fetal', 'circunferencia_cefalica_fetal_mm',
        'circunferencia_abdominal_mm', 'comprimento_femur_mm', 'glicemia_jejum', 'glicemia_pos_prandial',
        'hba1c'
    ]
    for col in numericas:
        if col in df.columns:
            df[col] = pd.to_numeric(df[col].astype(str).str.replace(',', '.', regex=False), errors='coerce')

    # ------------------------------
    # BOOLEANOS
    # ------------------------------
    bool_cols = [
        'diabetes_gestacional', 'obesidade_pre_gestacional', 'tabagismo', 'alcoolismo', 'chd_confirmada'
    ]
    for col in bool_cols:
        if col in df.columns:
            # Garante que nulos/vazios se tornem False após a conversão para bool
            df[col] = df[col].fillna(0).astype(str).str.strip().replace({'0': False, '1': True, 'TRUE': True, 'FALSE': False, '': False}).astype(bool)

    # ------------------------------
    # REMOVER LINHAS CRÍTICAS NULAS
    # ------------------------------
    colunas_essenciais = ['idade', 'imc', 'diabetes_gestacional']
    df.dropna(subset=[col for col in colunas_essenciais if col in df.columns], inplace=True)
    
    return df

if not df.empty:
    df = tratar_dados(df)

# ------------------------------
# GERAR HISTOGRAMA DE IDADE
# ------------------------------
if not df.empty and 'idade' in df.columns and not df['idade'].dropna().empty:
    counts, bins = np.histogram(df['idade'].dropna(), bins=10)
    dist_idade_data = {
        "labels": [f"{int(bins[i])}-{int(bins[i+1])}" for i in range(len(bins)-1)],
        "values": counts.tolist()
    }

    # Salvar imagem
    plt.figure(figsize=(6,4))
    plt.bar(dist_idade_data['labels'], dist_idade_data['values'], color='skyblue')
    plt.title('Distribuição de Idade')
    plt.ylabel('Quantidade de Gestantes')
    plt.xticks(rotation=45)
    histograma_path = os.path.join(public_dir, "histograma_idade.png")
    plt.tight_layout()
    plt.savefig(histograma_path)
    plt.close()
else:
    dist_idade_data = {"labels": [], "values": []}
    histograma_path = None

# ------------------------------
# CONTAGEM DE DIABETES
# ------------------------------
cont_diabetes = int(df['diabetes_gestacional'].sum()) if not df.empty and 'diabetes_gestacional' in df.columns else 0

# ------------------------------
# RESULTADO FINAL JSON
# ------------------------------
resultado = {
    "status": "concluido",
    "histograma_idade": dist_idade_data,
    "total_diabetes": cont_diabetes,
    "imagens": {}
}
if histograma_path:
    resultado['imagens']['Distribuicao_Idade'] = "/python_api/histograma_idade.png"

print(json.dumps(resultado, ensure_ascii=False, indent=4))