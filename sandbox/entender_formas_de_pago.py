import base64
import requests

# URL base de la API de Hiboutik
base_url = "https://triforfun.hiboutik.com/api"

# Credenciales de autenticación
username = "COMERCIAL@TRIFORFUN.ES"
api_key = "5VMW1UHW49FKHCFU5MM0QHSNXGUGO2OKN16"
auth_str = f"{username}:{api_key}"
auth_bytes = auth_str.encode("utf-8")
auth_base64 = base64.b64encode(auth_bytes).decode("utf-8")

headers = {
    "Authorization": f"Basic {auth_base64}",
    "Content-Type": "application/json"
}

def obtener_store_id():
    """Obtiene el store_id de la tienda."""
    url = f"{base_url}/stores"
    response = requests.get(url, headers=headers)
    if response.status_code == 200:
        stores = response.json()
        if stores:
            store_id = stores[0]['store_id']  # Asumiendo que tomamos el primer store_id
            return store_id
        else:
            print("No se encontraron tiendas.")
            return None
    else:
        print(f"Error al obtener el store_id: {response.status_code}")
        return None

def listar_formas_de_pago(store_id):
    """Lista las formas de pago disponibles en la tienda."""
    url = f"{base_url}/payment_types/{store_id}/"
    response = requests.get(url, headers=headers)
    if response.status_code == 200:
        tipos_de_pago = response.json()
        print("Respuesta completa de tipos de pago:", tipos_de_pago)  # Imprimir la respuesta completa
        for tipo in tipos_de_pago:
            print(f"ID: {tipo.get('payment_type_id', 'N/A')}, Nombre: {tipo.get('name', 'N/A')}")
    else:
        print(f"Error al obtener los tipos de pago: {response.status_code}")
        print(f"Detalle del error: {response.json()}")  # Imprimir detalles del error

def main():
    store_id = obtener_store_id()
    if store_id:
        listar_formas_de_pago(store_id)
    else:
        print("No se pudo obtener el store_id.")

if __name__ == "__main__":
    main()
