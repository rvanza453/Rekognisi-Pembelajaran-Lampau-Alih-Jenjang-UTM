from flask import Flask, request, jsonify
from flask_cors import CORS
from nltk.corpus import wordnet
from nltk.stem import WordNetLemmatizer
import nltk

# Download WordNet data
nltk.download('wordnet')
nltk.download('omw-1.4')

app = Flask(__name__)
CORS(app)

def get_word_synonyms(word):
    synonyms = set()
    lemmatizer = WordNetLemmatizer()
    
    # Lemmatize the word
    word = lemmatizer.lemmatize(word.lower())
    
    # Get English synonyms
    for syn in wordnet.synsets(word):
        # Add lemma names
        for lemma in syn.lemmas():
            synonyms.add(lemma.name())
        
        # Add hypernyms
        for hypernym in syn.hypernyms():
            for lemma in hypernym.lemmas():
                synonyms.add(lemma.name())
                
        # Add hyponyms
        for hyponym in syn.hyponyms():
            for lemma in hyponym.lemmas():
                synonyms.add(lemma.name())
                
        # Add meronyms (part-of relationships)
        for meronym in syn.part_meronyms():
            for lemma in meronym.lemmas():
                synonyms.add(lemma.name())
                
        # Add holonyms (whole-of relationships)
        for holonym in syn.part_holonyms():
            for lemma in holonym.lemmas():
                synonyms.add(lemma.name())
                
        # Add similar tos
        for similar in syn.similar_tos():
            for lemma in similar.lemmas():
                synonyms.add(lemma.name())

    return synonyms

@app.route('/synonyms', methods=['GET'])
def get_synonyms():
    phrase = request.args.get('word', '').lower()
    all_synonyms = set()
    
    # Comprehensive terms mapping by fields
    field_terms = {
        # Informatika & Komputer
        'kecerdasan buatan': ['artificial intelligence', 'ai', 'machine learning', 'ml', 
                             'deep learning', 'neural network', 'expert system', 'cognitive computing',
                             'intelligent systems', 'computational intelligence'],
        'basis data': ['database', 'db', 'data management', 'dbms', 'data storage', 'data repository',
                      'data warehouse', 'data system', 'information system'],
        'pembelajaran basis data': ['database learning', 'database education', 
                                  'database system learning', 'dbms learning', 'data management education',
                                  'database training', 'data system education'],
        'pemrograman': ['programming', 'coding', 'software development', 'software engineering',
                       'application development', 'code development', 'program development'],
        'pemrograman lanjut': ['advanced programming', 'advanced coding', 
                              'advanced software development', 'expert programming',
                              'professional programming', 'advanced software engineering'],
        'robotika': ['robotics', 'robot system', 'robotic engineering', 'automation engineering',
                    'mechatronics', 'automated systems', 'robotic systems'],
        'artificial intelligence': ['kecerdasan buatan', 'ai', 'machine learning', 
                                  'deep learning', 'neural network', 'cognitive computing',
                                  'intelligent systems', 'computational intelligence'],
        'analisis citra': ['image analysis', 'image processing', 'computer vision', 'visual computing',
                          'image recognition', 'visual analysis', 'digital image processing'],
        
        # Ekonomi & Bisnis
        'ekonomi makro': ['macroeconomics', 'macro economy', 'economic theory', 'economic analysis',
                         'economic science', 'economic study'],
        'pengantar ekonomi makro': ['introduction to macroeconomics', 
                                   'principles of macroeconomics', 
                                   'basic macroeconomics', 'fundamentals of macroeconomics',
                                   'macroeconomics basics', 'macroeconomics principles'],
        'akuntansi': ['accounting', 'bookkeeping', 'financial record', 'financial accounting',
                     'accountancy', 'financial management', 'financial reporting'],
        'manajemen': ['management', 'business administration', 'business management',
                     'organizational management', 'business leadership', 'corporate management'],
        'pemasaran': ['marketing', 'market research', 'business marketing', 'commercial marketing',
                     'product marketing', 'market analysis', 'marketing strategy'],
        
        # Teknik
        'mekanika': ['mechanics', 'mechanical engineering', 'mechanical science',
                    'mechanical systems', 'mechanical design', 'mechanical analysis'],
        'elektronika': ['electronics', 'electrical engineering', 'electronic engineering',
                       'electronic systems', 'electronic design', 'electronic technology'],
        'konstruksi': ['construction', 'building engineering', 'structural engineering',
                      'building construction', 'construction engineering', 'building design'],
        
        # Kesehatan
        'anatomi': ['anatomy', 'body structure', 'human anatomy', 'anatomical science',
                   'body organization', 'physical structure'],
        'farmakologi': ['pharmacology', 'drug study', 'medicinal chemistry', 'pharmaceutical science',
                       'drug research', 'medication study'],
        'keperawatan': ['nursing', 'healthcare', 'patient care', 'medical care',
                       'healthcare service', 'clinical care'],
        
        # Pendidikan
        'pedagogik': ['pedagogy', 'teaching method', 'educational methodology',
                     'teaching approach', 'educational practice', 'instructional method'],
        'kurikulum': ['curriculum', 'course design', 'educational program',
                     'academic program', 'study program', 'educational curriculum'],
        'pembelajaran': ['learning', 'education', 'teaching', 'instruction',
                        'educational process', 'knowledge acquisition'],
        
        # Umum
        'pengantar': ['introduction', 'principles', 'fundamentals', 'basics',
                     'preliminary', 'elementary', 'beginning'],
        'dasar': ['basic', 'fundamental', 'elementary', 'essential',
                 'primary', 'rudimentary', 'foundational'],
        'lanjut': ['advanced', 'intermediate', 'higher level', 'progressive',
                  'further', 'superior', 'elevated'],
        'praktikum': ['practicum', 'laboratory work', 'practical work', 'hands-on training',
                     'experimental work', 'practical training', 'lab work'],
        'analisis': ['analysis', 'analytics', 'examination', 'investigation',
                    'study', 'research', 'evaluation'],
        'sistem': ['system', 'framework', 'structure', 'organization',
                  'arrangement', 'setup', 'configuration'],
        'teori': ['theory', 'theoretical study', 'concept', 'principle',
                 'hypothesis', 'postulate', 'doctrine'],

        'hukum dagang': ['commercial law', 'trade law', 'business law', 'mercantile law'],
        'hukum perdata': ['civil law', 'private law'],
        'hukum pidana': ['criminal law', 'penal law'],
        'hukum internasional': ['international law', 'public international law', 'law of nations'],
        'hukum tata negara': ['constitutional law', 'state law'], # state administrative law bisa ambigu
        'hukum administrasi negara': ['administrative law', 'public administration law'],
        'hukum agraria': ['agrarian law', 'land law'],
        'hukum lingkungan': ['environmental law', 'ecology law'],
        'hukum perjanjian': ['contract law', 'law of obligations', 'agreement law'],
        'hukum islam': ['islamic law', 'sharia law'],
        'hukum ketenagakerjaan': ['labor law', 'employment law'],
        'hukum pemerintah daerah': ['local government law', 'regional governance law'],
        'hukum waris': ['inheritance law', 'law of succession'],
        'hukum e-commerce': ['e-commerce law', 'cyber law', 'digital trade law'], # perhatikan overlap dengan hukum siber umum
        'hukum surat berharga': ['securities law', 'negotiable instruments law'],
        'hukum perseroan': ['company law', 'corporate law'],
        'sosiologi hukum': ['sociology of law', 'legal sociology', 'socio-legal studies'],
        'kearifan lokal': ['local wisdom', 'indigenous knowledge', 'traditional practices'], # jika ini sering muncul bersama sosiologi hukum

    }

    # First check if the complete phrase exists in field_terms
    if phrase in field_terms:
        all_synonyms.update(field_terms[phrase])
    
    # Split the phrase and process each word
    words = phrase.split()
    
    # Process two-word combinations
    for i in range(len(words)-1):
        word_pair = ' '.join(words[i:i+2])
        if word_pair in field_terms:
            all_synonyms.update(field_terms[word_pair])
    
    # Process individual words
    for word in words:
        # Add WordNet synonyms
        wordnet_synonyms = get_word_synonyms(word)
        all_synonyms.update(wordnet_synonyms)
        
        # Add custom mappings if available
        if word in field_terms:
            all_synonyms.update(field_terms[word])
    
    # Clean up synonyms
    cleaned_synonyms = set()
    for syn in all_synonyms:
        # Replace underscores with spaces
        cleaned_syn = syn.replace('_', ' ')
        # Convert to lowercase
        cleaned_syn = cleaned_syn.lower()
        # Remove any special characters
        cleaned_syn = ''.join(e for e in cleaned_syn if e.isalnum() or e.isspace())
        if cleaned_syn:  # Only add non-empty strings
            cleaned_synonyms.add(cleaned_syn)
    
    # Add original phrase
    cleaned_synonyms.add(phrase)
    
    # Convert to list and sort
    result = sorted(list(cleaned_synonyms))
    
    return jsonify(result)

if __name__ == '__main__':
    print("Starting WordNet service...")
    print("Server running on http://localhost:5000")
    app.run(port=5000, debug=True)
