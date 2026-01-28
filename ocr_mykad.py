"""
Google Cloud Vision OCR script for MyKad extraction
Provides superior accuracy for Malaysian IC cards
"""
import sys
import json
import os
import re

# Suppress warnings
import warnings
warnings.filterwarnings('ignore')

from google.cloud import vision
import cv2
import numpy as np
import io

def preprocess_image(image_path):
    """Preprocess image to improve OCR accuracy - remove blue MyKad background"""
    img = cv2.imread(image_path)
    
    if img is None:
        return image_path
    
    # Split BGR channels
    b, g, r = cv2.split(img)
    
    # Remove blue channel completely (MyKad holographic background is blue)
    # Keep only red and green channels
    no_blue = cv2.merge([np.zeros_like(b), g, r])
    
    # Convert to grayscale
    gray = cv2.cvtColor(no_blue, cv2.COLOR_BGR2GRAY)
    
    # Apply CLAHE (Contrast Limited Adaptive Histogram Equalization) with stronger settings
    clahe = cv2.createCLAHE(clipLimit=3.0, tileGridSize=(8,8))
    enhanced = clahe.apply(gray)
    
    # Apply bilateral filter to reduce noise while keeping edges
    denoised = cv2.bilateralFilter(enhanced, 9, 75, 75)
    
    # Apply sharpening to improve digit clarity
    kernel = np.array([[-1,-1,-1], [-1,9,-1], [-1,-1,-1]])
    sharpened = cv2.filter2D(denoised, -1, kernel)
    
    # Apply adaptive thresholding with optimized parameters for digits
    binary = cv2.adaptiveThreshold(sharpened, 255, cv2.ADAPTIVE_THRESH_GAUSSIAN_C, cv2.THRESH_BINARY, 11, 2)
    
    # Apply morphological operations to clean up
    kernel = np.ones((2,2), np.uint8)
    binary = cv2.morphologyEx(binary, cv2.MORPH_CLOSE, kernel)
    
    # Save preprocessed image
    temp_path = image_path.replace('.', '_processed.')
    cv2.imwrite(temp_path, binary)
    
    return temp_path

def extract_mykad_data(image_path):
    """Extract data from MyKad using Google Cloud Vision OCR"""
    try:
        # Initialize the Vision API client
        client = vision.ImageAnnotatorClient()
        
        all_results = []
        
        # Strategy 1: Try with preprocessed image first (removes blue background)
        # This helps detect IC numbers which are often obscured by holographic background
        preprocessed_path = preprocess_image(image_path)
        if preprocessed_path != image_path:
            with io.open(preprocessed_path, 'rb') as image_file:
                content = image_file.read()
            
            image_preprocessed = vision.Image(content=content)
            image_context = vision.ImageContext(language_hints=['en', 'ms'])
            
            # Try both text_detection and document_text_detection
            response_text = client.text_detection(image=image_preprocessed, image_context=image_context)
            response_doc = client.document_text_detection(image=image_preprocessed, image_context=image_context)
            
            if response_text.text_annotations:
                all_results.append(('preprocessed_text', response_text.text_annotations[0].description if response_text.text_annotations else ''))
            
            if response_doc.full_text_annotation.text:
                all_results.append(('preprocessed_doc', response_doc.full_text_annotation.text))
        
        # Strategy 2: Try with original image
        with io.open(image_path, 'rb') as image_file:
            content = image_file.read()
        
        image = vision.Image(content=content)
        image_context = vision.ImageContext(language_hints=['en', 'ms'])
        
        # Try both detection methods
        response_text = client.text_detection(image=image, image_context=image_context)
        response_doc = client.document_text_detection(image=image, image_context=image_context)
        
        if response_text.text_annotations:
            all_results.append(('original_text', response_text.text_annotations[0].description if response_text.text_annotations else ''))
        
        if response_doc.full_text_annotation.text:
            all_results.append(('original_doc', response_doc.full_text_annotation.text))
        
        # Check for errors
        if response_doc.error.message:
            raise Exception(f'{response_doc.error.message}')
        
        if not all_results:
            return {
                'success': False,
                'error': 'No text detected in image'
            }
        
        # Choose the best result - prioritize preprocessed text (better for IC numbers)
        # Look for IC number pattern to determine best result
        best_text = ''
        best_source = ''
        
        for source, text in all_results:
            if text and len(text) > len(best_text):
                best_text = text
                best_source = source
            # Prioritize results that contain IC number pattern
            if text and re.search(r'\d{6}[-\s]?\d{2}[-\s]?\d{4}', text):
                best_text = text
                best_source = source
                break
        
        document_text = best_text
        
        # Extract text by lines
        text_lines = []
        all_text = []
        
        # Keywords to exclude (card header text)
        exclude_keywords = ['KAD', 'PENGENALAN', 'IDENTITY', 'CARD', 'MYKAD', 'MKAD', 'MALAYSIA', 'MALA']
        
        # Split text into lines and process
        for line in document_text.split('\n'):
            line = line.strip()
            if not line:
                continue
                
            all_text.append(line)
            
            # Skip card header keywords
            if any(keyword in line.upper() for keyword in exclude_keywords):
                continue
            
            text_lines.append(line)
        
        # Join all text
        full_text = '\n'.join(text_lines)
        
        # Return results
        return {
            'success': True,
            'text': full_text,
            'lines': text_lines,
            'all_detections': all_text,  # For debugging
            'confidence': 'high',
            'image_source': best_source  # Which detection method worked best
        }
        
    except Exception as e:
        return {
            'success': False,
            'error': str(e)
        }

if __name__ == '__main__':
    if len(sys.argv) < 2:
        print(json.dumps({'success': False, 'error': 'No image path provided'}))
        sys.exit(1)
    
    image_path = sys.argv[1]
    result = extract_mykad_data(image_path)
    
    # Print only JSON output
    print(json.dumps(result, ensure_ascii=True))
