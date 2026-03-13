# ==============================
# 1. IMPORTAR BIBLIOTECAS
# ==============================

import pandas as pd
import numpy as np
import os
from scipy.stats import shapiro, mannwhitneyu, chi2_contingency
from warnings import filterwarnings
import json

filterwarnings('ignore')

# ==============================
# 2. CARREGAR BASE DE DADOS
# ==============================

BASE_DIR = os.path.dirname(__file__)
csv_path = os.path.join(BASE_DIR, "base_completa_final.csv")

df = pd.read_csv(csv_path, sep=";")

df['chd_confirmada'] = df['chd_confirmada'].astype(int)

# ==============================
# 3. GERAR DADOS SINTÉTICOS
# ==============================

def gerar_dados_sinteticos(df_original, valor_chd, n_amostras):

    df_base = df_original[df_original['chd_confirmada'] == valor_chd]

    if df_base.empty:
        df_base = df_original[df_original['chd_confirmada'] != valor_chd]

    novos_dados = {}

    colunas_numericas = df_original.select_dtypes(include=np.number).columns.drop(
        ['gestante_id', 'consulta_numero', 'chd_confirmada'],
        errors='ignore'
    )

    colunas_categoricas = df_original.select_dtypes(include=['object', 'category']).columns

    for col in colunas_numericas:
        amostra = df_base[col].dropna().sample(n_amostras, replace=True).values
        novos_dados[col] = amostra

    for col in colunas_categoricas:
        freq = df_base[col].value_counts(normalize=True)
        amostra = np.random.choice(freq.index, size=n_amostras, p=freq.values)
        novos_dados[col] = amostra

    novos_dados['chd_confirmada'] = valor_chd

    return pd.DataFrame(novos_dados)


df_sintetico = gerar_dados_sinteticos(df, 0, 135)
df = pd.concat([df, df_sintetico], ignore_index=True)

# ==============================
# 4. CRIAR DIRETÓRIO OUTPUT
# ==============================

output_dir = os.path.join(BASE_DIR, "output")
os.makedirs(output_dir, exist_ok=True)

# ==============================
# 5. INFORMAÇÕES GERAIS
# ==============================

missing_values = pd.DataFrame({
    "faltantes": df.isnull().sum(),
    "percentual": (df.isnull().sum() / len(df)) * 100
})

# missing_values.to_csv(os.path.join(output_dir, "valores_faltantes.csv"))

# ==============================
# 6. ESTATÍSTICA DESCRITIVA
# ==============================

numericas = df.select_dtypes(include=['float64', 'int64'])

estatisticas = numericas.describe().T

# estatisticas.to_csv(os.path.join(output_dir, "estatisticas_descritivas.csv"))

# ==============================
# 7. TESTE DE NORMALIDADE
# ==============================

normalidade = {}

for col in numericas.columns:

    dados = df[col].dropna()

    if len(dados) > 3:
        stat, p = shapiro(dados)
        normalidade[col] = p

normalidade_df = pd.DataFrame.from_dict(
    normalidade,
    orient="index",
    columns=["p_valor"]
)

# normalidade_df.to_csv(os.path.join(output_dir, "teste_normalidade.csv"))

# ==============================
# 8. SEPARAR GRUPOS
# ==============================

chd = df[df['chd_confirmada'] == 1]
sem_chd = df[df['chd_confirmada'] == 0]

# ==============================
# 9. TESTE MANN-WHITNEY
# ==============================

variaveis = [
    'idade',
    'imc',
    'pressao_sistolica',
    'frequencia_cardiaca_fetal',
    'idade_gestacional'
]

resultados = []

for var in variaveis:

    if var in df.columns:

        grupo1 = chd[var].dropna()
        grupo2 = sem_chd[var].dropna()

        if len(grupo1) > 0 and len(grupo2) > 0:

            stat, p = mannwhitneyu(grupo1, grupo2)

            resultados.append([var, p])

teste_mw = pd.DataFrame(
    resultados,
    columns=["variavel", "p_valor"]
)

# teste_mw.to_csv(os.path.join(output_dir, "teste_mann_whitney.csv"), index=False)

# ==============================
# 10. TESTE QUI-QUADRADO
# ==============================

categoricas = [
    'diabetes_gestacional',
    'hipertensao',
    'hipertensao_pre_eclampsia',
    'obesidade_pre_gestacional',
    'tabagismo',
    'alcoolismo'
]

resultados_chi = []

for var in categoricas:

    if var in df.columns:

        tabela = pd.crosstab(df[var], df['chd_confirmada'])

        if tabela.shape[0] > 1:

            chi2, p, dof, exp = chi2_contingency(tabela)

            resultados_chi.append([var, p])

chi_df = pd.DataFrame(
    resultados_chi,
    columns=["variavel", "p_valor"]
)

# chi_df.to_csv(os.path.join(output_dir, "teste_quiquadrado.csv"), index=False)

# ==============================
# 11. CORRELAÇÃO
# ==============================

corr = numericas.corr(method="spearman")

# corr.to_csv(os.path.join(output_dir, "correlacao_spearman.csv"))

# ==============================
# 12. PREPARAR DADOS PARA JSON
# ==============================

# Dados para o histograma de idade
counts, bins = np.histogram(df['idade'].dropna(), bins=10)
dist_idade_data = {
    "labels": [f"{int(bins[i])}-{int(bins[i+1])}" for i in range(len(bins)-1)],
    "values": counts.tolist()
}

# Dados para o boxplot de IMC
imc_chd_data = {
    "com_chd": chd['imc'].dropna().tolist(),
    "sem_chd": sem_chd['imc'].dropna().tolist()
}

# Agrupar todos os dados em um dicionário
output_data = {
    "tabelas": {
        "valores_faltantes": missing_values.reset_index().rename(columns={'index': 'variavel'}).to_dict(orient='records'),
        "estatisticas_descritivas": estatisticas.reset_index().rename(columns={'index': 'variavel'}).to_dict(orient='records'),
        "teste_normalidade": normalidade_df.reset_index().rename(columns={'index': 'variavel'}).to_dict(orient='records'),
        "teste_mann_whitney": teste_mw.to_dict(orient='records'),
        "teste_quiquadrado": chi_df.to_dict(orient='records'),
        "correlacao_spearman": corr.reset_index().rename(columns={'index': 'variavel'}).to_dict(orient='records')
    },
    "graficos": {
        "distribuicao_idade": dist_idade_data,
        "imc_por_chd": imc_chd_data
    }
}


# ==============================
# 13. FINALIZAÇÃO
# ==============================

json_path = os.path.join(output_dir, "dashboard_data.json")
with open(json_path, 'w', encoding='utf-8') as f:
    json.dump(output_data, f, ensure_ascii=False, indent=4)

print("Análise concluída.")
print(f"Resultados salvos em: {json_path}")