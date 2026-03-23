import re

def search_index():
    with open('index.html', 'r', encoding='utf-8') as f:
        content = f.read()

    print(f"File size: {len(content)} bytes")

    # Search for infixs inputs
    inputs = re.findall(r'<input[^>]*infixs[^>]*>', content)
    if inputs:
        print(f"Found {len(inputs)} infixs inputs:")
        for i in inputs:
            print(i)
    else:
        print("No inputs with 'infixs' found.")

    # Search for "Rastre"
    rastre = re.findall(r'.{0,50}rastre.{0,50}', content, re.IGNORECASE)
    if rastre:
        print(f"Found {len(rastre)} matches for 'rastre':")
        for i in rastre[:5]:
            print(i)
    else:
        print("No matches for 'rastre' found.")
        
    # Search for "Cota"
    cota = re.findall(r'.{0,50}cota.{0,50}', content, re.IGNORECASE)
    if cota:
        print(f"Found {len(cota)} matches for 'cota':")
        for i in cota[:5]:
            print(i)

if __name__ == "__main__":
    search_index()
