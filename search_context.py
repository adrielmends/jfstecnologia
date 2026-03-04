import re

def search_context():
    with open('index.html', 'r', encoding='utf-8') as f:
        content = f.read()

    match = re.search(r'Solicitar-11-1[^"\')]*', content, re.IGNORECASE)
    if match:
        print(f"Found image: {match.group(0)}")
        start = max(0, match.start() - 1000)
        end = min(len(content), match.end() + 1000)
        print(f"Context:\n{content[start:end]}")
    else:
        print("Image not found.")

if __name__ == "__main__":
    search_context()
