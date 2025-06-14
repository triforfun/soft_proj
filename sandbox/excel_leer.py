import os
import pandas as pd

# Verifica si el fichero fich2.csv existe y, en caso afirmativo, lo borra
if os.path.exists(fich2.csv):
    os.remove(fich2.csv)

# Lee el fichero de entrada (fich1.xlsx)
df = pd.read_excel(fich1.xlsx')

# Selecciona las columnas 2 y 5
selected_columns = df.iloc[:, [1, 4]]

# Renombra las columnas
selected_columns.columns = ['codigo_barras', 'stock_proveedor']

# Guarda el resultado en un nuevo fichero (fich2.csv)
selected_columns.to_csv(fich2.csv, index=False, sep=';', encoding='utf-8')
