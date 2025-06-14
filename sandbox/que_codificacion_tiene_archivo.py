"""
detectar codifcacion del archivo csv
"""
import chardet
import pandas as pd

# Ruta del archivo CSV
# file_path =  r'C:\TFF\DOCS\ONLINE\STOCKS_EXTERNS\Availability.csv'

file_path =  r'C:\TFF\DOCS\ONLINE\STOCKS_EXTERNS\STOCKSSD.csv'

# Detectar la codificación del archivo
with open(file_path, 'rb') as f:
    rawdata = f.read(10000)
    result = chardet.detect(rawdata)
    encoding = result['encoding']

print(f"La codificación detectada es: {encoding}")

# Leer el archivo CSV con la codificación detectada
df = pd.read_csv(file_path, encoding=encoding)

# Mostrar las primeras filas del DataFrame
print(df.head())

# Mostrar los valores de la columna 'Código de barras'
if 'Variant Id' in df.columns:
    print(df['Variant Id'].head())
else:
    print("La columna 'Variant Id' no se encuentra en el archivo CSV.")