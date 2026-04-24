<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Metalúrgica Tech - Kratos AI</title>
    <style>
        /* Estilos baseados no index1.html */
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        
        body { 
            background-color: #1a1a1a; 
            display: flex; 
            flex-direction: column; 
            justify-content: center; 
            align-items: center; 
            height: 100vh; 
            gap: 15px; 
        }

        /* Card de Configuração da API (Estilo Kratos) */
        #api-card { 
            width: 400px; 
            background: #2c3e50; 
            padding: 10px 15px; 
            border-radius: 10px; 
            border-left: 5px solid #e67e22; 
            display: flex; 
            gap: 10px; 
            align-items: center;
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
        }
        #api-key-input { 
            flex: 1; 
            background: #34495e; 
            border: 1px solid #7f8c8d; 
            color: white; 
            padding: 8px; 
            border-radius: 5px; 
            font-size: 12px; 
            outline: none;
        }
        #api-card button { 
            background: #e67e22; 
            border: none; 
            color: white; 
            padding: 8px 12px; 
            border-radius: 5px; 
            cursor: pointer; 
            font-weight: bold; 
            transition: 0.3s;
        }
        #api-card button:hover { background: #d35400; }

        /* Container do Chat */
        #chat-container { 
            width: 400px; 
            height: 550px; 
            background: #ffffff; 
            border-radius: 15px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.5); 
            display: flex; 
            flex-direction: column; 
            overflow: hidden; 
        }

        #chat-header { 
            background: #2c3e50; 
            color: white; 
            padding: 15px; 
            display: flex; 
            align-items: center; 
            justify-content: space-between; 
            border-bottom: 4px solid #e67e22; 
        }

        .brand { display: flex; align-items: center; gap: 10px; }
        .logo-box { 
            width: 35px; 
            height: 35px; 
            background: #e67e22; 
            border-radius: 5px; 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            font-weight: bold; 
            font-size: 20px; 
            color: white; 
        }

        #chat-window { 
            flex: 1; 
            padding: 20px; 
            overflow-y: auto; 
            background: #f4f4f4; 
            display: flex; 
            flex-direction: column; 
            gap: 12px; 
        }

        /* Balões de Mensagem */
        .msg { 
            padding: 10px 14px; 
            border-radius: 15px; 
            font-size: 14px; 
            max-width: 85%; 
            line-height: 1.4; 
            word-wrap: break-word;
        }
        .user { 
            background: #3498db; 
            color: white; 
            align-self: flex-end; 
            border-bottom-right-radius: 2px; 
        }
        .bot { 
            background: #ffffff; 
            color: #2c3e50; 
            align-self: flex-start; 
            border-bottom-left-radius: 2px; 
            border: 1px solid #dcdde1; 
            font-weight: 500;
        }

        /* Área de Input */
        #input-area { 
            display: flex; 
            padding: 15px; 
            background: #fff; 
            border-top: 1px solid #eee; 
        }
        #user-input { 
            flex: 1; 
            padding: 10px 15px; 
            border: 1px solid #ddd; 
            border-radius: 20px; 
            outline: none; 
        }
        #send-btn { 
            background: #27ae60; 
            color: white; 
            border: none; 
            padding: 0 20px; 
            margin-left: 8px; 
            border-radius: 20px; 
            cursor: pointer; 
            font-weight: bold; 
            transition: 0.3s;
        }
        #send-btn:hover { background: #219150; }
        
        .loading { font-style: italic; color: #7f8c8d; font-size: 12px; }
    </style>
</head>
<body>

    <div id="api-card">
        <input type="password" id="api-key-input" placeholder="Insira a Chave do Olimpo (Groq API)">
        <button onclick="configurarAPI()">ATIVAR</button>
    </div>

    <div id="chat-container">
        <div id="chat-header">
            <div class="brand">
                <div class="logo-box">Ω</div>
                <span>Kratos Industrial</span>
            </div>
            <div id="status-dot" style="width: 10px; height: 10px; background: #e74c3c; border-radius: 50%;"></div>
        </div>

        <div id="chat-window">
            <div class="msg bot">Mortal! Para que eu possa forjar suas respostas, você deve primeiro inserir a chave secreta de Groq acima!</div>
        </div>

        <div id="input-area">
            <input type="text" id="user-input" placeholder="Sua dúvida de metalurgia..." autocomplete="off">
            <button id="send-btn">ENVIAR</button>
        </div>
    </div>

    <script>
        let GROQ_API_KEY = "";
        const chatWindow = document.getElementById('chat-window');
        const userInput = document.getElementById('user-input');
        
        // Comportamento do Kratos (System Prompt)
        const SYSTEM_PROMPT = "Você é o Kratos de God of War, mas agora você trabalha na Metalúrgica Tech. Você é um mestre da metalurgia, soldagem e mecânica industrial. Fale de forma épica, bruta, curta e autoritária. Use termos como 'Mortal', 'Pelos Deuses!', 'A fúria da forja!'. Responda apenas sobre temas industriais.";

        let historico = [{ role: "system", content: SYSTEM_PROMPT }];

        function configurarAPI() {
            const key = document.getElementById('api-key-input').value.trim();
            if (key) {
                GROQ_API_KEY = key;
                document.getElementById('status-dot').style.background = "#2ecc71";
                document.getElementById('api-key-input').disabled = true;
                appendMsg("A chave foi aceita! A forja está pronta para o seu comando!", "bot");
            } else {
                alert("Insira uma chave válida, mortal!");
            }
        }

        async function enviarMensagem() {
            const text = userInput.value.trim();
            if (!text) return;
            if (!GROQ_API_KEY) {
                appendMsg("VOCÊ É TOLHO? Insira a chave da API primeiro!", "bot");
                return;
            }

            appendMsg(text, 'user');
            historico.push({ role: "user", content: text });
            userInput.value = "";

            const loadingMsg = document.createElement('div');
            loadingMsg.className = 'msg bot loading';
            loadingMsg.innerText = "Kratos está consultando as runas...";
            chatWindow.appendChild(loadingMsg);
            chatWindow.scrollTop = chatWindow.scrollHeight;

            try {
                const response = await fetch("https://api.groq.com/openai/v1/chat/completions", {
                    method: "POST",
                    headers: {
                        "Authorization": `Bearer ${GROQ_API_KEY}`,
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({
                        model: "llama-3.3-70b-versatile",
                        messages: historico,
                        temperature: 0.6
                    })
                });

                const data = await response.json();
                chatWindow.removeChild(loadingMsg);

                if (data.choices && data.choices[0]) {
                    const resposta = data.choices[0].message.content;
                    appendMsg(resposta, "bot");
                    historico.push({ role: "assistant", content: resposta });
                } else {
                    throw new Error();
                }

            } catch (error) {
                if(chatWindow.contains(loadingMsg)) chatWindow.removeChild(loadingMsg);
                appendMsg("A fúria de Zeus interrompeu a conexão! Verifique sua chave.", "bot");
            }
        }

        function appendMsg(text, side) {
            const div = document.createElement('div');
            div.className = `msg ${side}`;
            div.innerText = text;
            chatWindow.appendChild(div);
            chatWindow.scrollTop = chatWindow.scrollHeight;
        }

        document.getElementById('send-btn').onclick = enviarMensagem;
        userInput.onkeypress = (e) => { if (e.key === 'Enter') enviarMensagem(); };
    </script>
</body>
</html>