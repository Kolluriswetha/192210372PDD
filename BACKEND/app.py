import os
import json
from http.server import BaseHTTPRequestHandler, HTTPServer
import google.generativeai as genai
class MyHandler(BaseHTTPRequestHandler):  
    def do_POST(self):
        content_length = int(self.headers['Content-Length'])
        post_data = self.rfile.read(content_length)
        received_data = json.loads(post_data.decode('utf-8'))

        print("Received data from PHP:", received_data)

        # Configure Gemini API
        genai.configure(api_key="AIzaSyBTDK8vDv5SzXKx7U9LlpSS0uTHatq_0Qk")

        generation_config = {
            "temperature": 1,
            "top_p": 0.95,
            "top_k": 40,
            "max_output_tokens": 8192,
            "response_mime_type": "text/plain",
        }

        model = genai.GenerativeModel(
            model_name="gemini-2.5-flash",
            generation_config=generation_config,
            system_instruction=f"You are an author. Create an interesting short story using the words: '{received_data['name']}', genre: '{received_data['genre']}', and tone: '{received_data['tone']}'. Use simple English and keep the story engaging."
        )

        chat_session = model.start_chat(history=[])
        response = chat_session.send_message(f"Write a story using the words: {received_data['name']}")

        print("Generated Response:", response.text)

        response_data = {
            "reply": response.text,
            "name": received_data["name"],
            "genre": received_data["genre"],
            "tone": received_data["tone"]
        }

        response_json = json.dumps(response_data)
        self.send_response(200)
        self.send_header('Content-Type', 'application/json')
        self.end_headers()
        self.wfile.write(response_json.encode('utf-8'))

# Run the server
server_address = ('', 8000)
httpd = HTTPServer(server_address, MyHandler)
print("Python server is running on port 8000...")
httpd.serve_forever()