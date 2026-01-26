"""
EasyOCR script for MyKad extraction
More accurate than Tesseract for Malaysian IC cards
"""
import sys
import json
import os

# Suppress progress bars and warnings
os.environ['TF_CPP_MIN_LOG_LEVEL'] = '3'
import warnings
warnings.filterwarnings('ignore')

import easyocr
import cv2
import numpy as np

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
    
    # Apply CLAHE (Contrast Limited Adaptive Histogram Equalization)
    clahe = cv2.createCLAHE(clipLimit=2.0, tileGridSize=(8,8))
    enhanced = clahe.apply(gray)
    
    # Apply bilateral filter to reduce noise while keeping edges
    denoised = cv2.bilateralFilter(enhanced, 9, 75, 75)
    
    # Apply adaptive thresholding
    binary = cv2.adaptiveThreshold(denoised, 255, cv2.ADAPTIVE_THRESH_GAUSSIAN_C, cv2.THRESH_BINARY, 11, 2)
    
    # Save preprocessed image
    temp_path = image_path.replace('.', '_processed.')
    cv2.imwrite(temp_path, binary)
    
    return temp_path

def extract_mykad_data(image_path):
    """Extract data from MyKad using EasyOCR"""
    try:
        # Initialize reader with better settings
        reader = easyocr.Reader(['en'], gpu=False, verbose=False)
        
        # Try multiple detection strategies
        all_results = []
        
        # Strategy 1: Original with fine-tuned parameters
        results1 = reader.readtext(image_path, detail=1, paragraph=False,
                                   width_ths=0.7, height_ths=0.5,
                                   mag_ratio=1.5, text_threshold=0.7)
        all_results.extend(results1)
        
        # Strategy 2: Lower thresholds for difficult text
        results2 = reader.readtext(image_path, detail=1, paragraph=False,
                                   width_ths=0.5, height_ths=0.5,
                                   mag_ratio=1.0, text_threshold=0.5)
        all_results.extend(results2)
        
        # Deduplicate by position (keep highest confidence)
        unique_results = {}
        for (bbox, text, confidence) in all_results:
            # Use top-left corner as position key
            pos_key = (int(bbox[0][0]/10), int(bbox[0][1]/10))  # Group by 10px grid
            if pos_key not in unique_results or unique_results[pos_key][2] < confidence:
                unique_results[pos_key] = (bbox, text, confidence)
        
        results = list(unique_results.values())
        
        # Sort by vertical position (top to bottom)
        results.sort(key=lambda x: x[0][0][1])
        
        # Extract text with confidence scores
        text_lines = []
        all_text = []  # Keep all for debugging
        
        # Keywords to exclude (card header text)
        exclude_keywords = ['KAD', 'PENGENALAN', 'IDENTITY', 'CARD', 'MYKAD', 'MKAD', 'MALAYSIA', 'MALA']
        
        for (bbox, text, confidence) in results:
            all_text.append(f"{text} ({confidence:.2f})")
            
            if confidence > 0.2:  # Lower threshold to catch address
                # Clean up text
                text = text.strip()
                
                # Skip card header keywords
                if any(keyword in text.upper() for keyword in exclude_keywords):
                    continue
                
                # Remove common OCR artifacts
                text = text.replace('|', 'I').replace('©', 'O')
                
                text_lines.append(text)
        
        # Join all text
        full_text = '\n'.join(text_lines)
        
        # Return results
        return {
            'success': True,
            'text': full_text,
            'lines': text_lines,
            'all_detections': all_text,  # For debugging
            'confidence': 'high' if len([r for r in results if r[2] > 0.7]) > 5 else 'medium'
        }
        
    except Exception as e:
        return {
            'success': False,
            'error': str(e)
        }

if __name__ == '__main__':
    # Redirect stderr to suppress progress bars
    import io
    sys.stderr = io.StringIO()
    
    if len(sys.argv) < 2:
        print(json.dumps({'success': False, 'error': 'No image path provided'}))
        sys.exit(1)
    
    image_path = sys.argv[1]
    result = extract_mykad_data(image_path)
    
    # Print only JSON output
    print(json.dumps(result, ensure_ascii=True))
