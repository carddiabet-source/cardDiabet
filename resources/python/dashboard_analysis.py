import sys
import os
import io
import base64

# Correção de ambiente
if os.name == 'nt':
    if 'USERPROFILE' not in os.environ:
        os.environ['USERPROFILE'] = os.environ.get('TEMP', os.getcwd())

if 'MPLCONFIGDIR' not in os.environ:
    os.environ['MPLCONFIGDIR'] = os.path.join(os.environ.get('TEMP', '/tmp'), 'matplotlib_config')

import matplotlib
matplotlib.use('Agg')

import pandas as pd
import matplotlib.pyplot as plt
import seaborn as sns


# ==============================
# FUNÇÃO PARA SALVAR GRÁFICOS
# ==============================

def salvar_grafico():

    buf = io.BytesIO()

    plt.tight_layout()
    plt.savefig(buf, format='png')

    plt.clf()
    plt.close()

    buf.seek(0)

    img_base64 = base64.b64encode(buf.read()).decode('utf-8')

    print(f'''
    <div style="background:white;padding:10px;border-radius:8px;
    box-shadow:0 0 5px rgba(0,0,0,0.1);">
        <img src="data:image/png;base64,{img_base64}" style="width:100%;height:auto;">
    </div>
    ''')


# ==============================
# FUNÇÃO PARA GERAR GRÁFICOS
# ==============================

def gerar_graficos(df):

    variaveis_numericas = [
        'idade_gestacional',
        'pressao_sistolica',
        'frequencia_cardiaca_fetal'
    ]

    variaveis_categoricas = [
        'diabetes_gestacional',
        'hipertensao',
        'tabagismo',
        'alcoolismo',
        'chd_confirmada'
    ]

    # ======================
    # HISTOGRAMAS
    # ======================

    for var in variaveis_numericas:

        if var not in df.columns:
            continue

        plt.figure(figsize=(6,4))

        sns.histplot(
            data=df,
            x=var,
            hue='chd_confirmada',
            kde=True
        )

        plt.title(f"Distribuição de {var}")

        salvar_grafico()


    # ======================
    # BOXPLOT
    # ======================

    for var in variaveis_numericas:

        if var not in df.columns:
            continue

        plt.figure(figsize=(6,4))

        sns.boxplot(
            data=df,
            x='chd_confirmada',
            y=var
        )

        plt.title(f"{var} por CHD")

        salvar_grafico()


    # ======================
    # BARRAS CATEGÓRICAS
    # ======================

    for var in variaveis_categoricas:

        if var not in df.columns:
            continue

        plt.figure(figsize=(6,4))

        df[var].value_counts().plot(kind='bar')

        plt.title(f"Frequência de {var}")

        salvar_grafico()


    # ======================
    # HEATMAP DE CORRELAÇÃO
    # ======================

    plt.figure(figsize=(6,5))

    corr = df.corr(numeric_only=True)

    sns.heatmap(
        corr,
        annot=True,
        cmap='coolwarm'
    )

    plt.title("Mapa de Correlação")

    salvar_grafico()


# ==============================
# MAIN
# ==============================

def main():

    if len(sys.argv) < 2:
        print("Use: python analise.py arquivo.csv")
        return

    caminho = sys.argv[1]

    if not os.path.exists(caminho):
        print("Arquivo não encontrado.")
        return

    df = pd.read_csv(caminho, sep=";")

    # GRID COM 3 GRÁFICOS POR LINHA
    print('''
    <div style="
    display:grid;
    grid-template-columns:repeat(3,1fr);
    gap:20px;
    padding:20px;
    ">
    ''')

    gerar_graficos(df)

    print("</div>")


if __name__ == "__main__":
    main()