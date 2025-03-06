from flask import Flask, request, render_template, redirect, url_for, send_file
import os
import fitz  # PyMuPDF
import pytesseract
from PIL import Image

app = Flask(__name__)
app.config['UPLOAD_FOLDER'] = 'uploads'
app.config['TEXT_FOLDER'] = 'text_files'

# Set Tesseract path (update if necessary)
pytesseract.pytesseract.tesseract_cmd = r'C:\Program Files\Tesseract-OCR\tesseract.exe'

# Ensure the upload and text folders exist
if not os.path.exists(app.config['UPLOAD_FOLDER']):
    os.makedirs(app.config['UPLOAD_FOLDER'])
if not os.path.exists(app.config['TEXT_FOLDER']):
    os.makedirs(app.config['TEXT_FOLDER'])

def extract_text_from_pdf(pdf_path):
    doc = fitz.open(pdf_path)
    text = ""
    for page_num in range(len(doc)):
        page = doc.load_page(page_num)
        pix = page.get_pixmap()
        img_path = os.path.join(app.config['UPLOAD_FOLDER'], f"page_{page_num}.png")
        pix.save(img_path)
        img = Image.open(img_path)
        text += pytesseract.image_to_string(img) + "\n"
    return text

@app.route('/', methods=['GET', 'POST'])
def index():
    extracted_text = ""
    text_file_path = ""
    if request.method == 'POST':
        if 'file' not in request.files:
            return redirect(request.url)
        file = request.files['file']
        if file.filename == '':
            return redirect(request.url)

        # Save the uploaded file
        file_path = os.path.join(app.config['UPLOAD_FOLDER'], file.filename)
        file.save(file_path)

        # Extract text from the PDF
        extracted_text = extract_text_from_pdf(file_path)

        # Save the extracted text to a file in the text_files folder
        text_file_path = os.path.join(app.config['TEXT_FOLDER'], f"{os.path.splitext(file.filename)[0]}.txt")
        with open(text_file_path, 'w', encoding='utf-8') as text_file:
            text_file.write(extracted_text)

        # Clean up uploaded files
        os.remove(file_path)
        for f in os.listdir(app.config['UPLOAD_FOLDER']):
            os.remove(os.path.join(app.config['UPLOAD_FOLDER'], f))

    return render_template('index.html', text=extracted_text, text_file_path=text_file_path)

@app.route('/download')
def download():
    text_file_path = request.args.get('text_file_path')
    if text_file_path and os.path.exists(text_file_path):
        return send_file(text_file_path, as_attachment=True)
    return "File not found."

if __name__ == '__main__':
    app.run(debug=True)
