import re

def search_content():
    with open('index.html', 'r', encoding='utf-8') as f:
        content = f.read()

    # Find stats about specific terms
    print("--- Searching for Background ---")
    bg_match = re.search(r'Backgrouend[^"\')]*', content, re.IGNORECASE)
    if bg_match:
        print(f"Found background image reference: {bg_match.group(0)}")
        # Print context
        start = max(0, bg_match.start() - 100)
        end = min(len(content), bg_match.end() + 100)
        print(f"Context: ...{content[start:end]}...")
    else:
        print("Background image 'Backgrouend' not found in text.")

    print("\n--- Searching for Card Section ---")
    # Search for "CALCULAR" to find the card section text
    card_match = re.search(r'CALCULAR FRETES', content, re.IGNORECASE)
    if card_match:
        print("Found 'CALCULAR FRETES'.")
        # Try to grab the surrounding HTML to understand structure
        start = max(0, card_match.start() - 500)
        end = min(len(content), card_match.end() + 500)
        print(f"Context: ...{content[start:end]}...")
    else:
        print("'CALCULAR FRETES' not found.")

    print("\n--- Searching for Solicitar Images ---")
    imgs = re.findall(r'src=["\']([^"\']*Solicitar[^"\']*)["\']', content, re.IGNORECASE)
    for img in imgs[:5]: # Show first 5
        print(f"Found image: {img}")

if __name__ == "__main__":
    search_content()
