let questions = [
   
    {
        question: "Türkiye'nin başkenti neresidir?",
        options: ["İstanbul", "Ankara", "İzmir", "Bursa"],
        correct: 1
    },
    {
        question: "Dünya'nın en uzun nehri hangisidir?",
        options: ["Amazon", "Nil", "Mississippi", "Yangtze"],
        correct: 1
    },
    {
        question: "En kalabalık şehir hangisidir?",
        options: ["Tokyo", "New York", "Mumbai", "Shanghai"],
        correct: 0
    },
    {
        question: "Roma İmparatorluğu'nun başkenti neresiydi?",
        options: ["Atina", "Roma", "İstanbul", "Paris"],
        correct: 1
    },
    {
        question: "ABD'nin en büyük eyaleti hangisidir?",
        options: ["Teksas", "Alaska", "Kaliforniya", "Montana"],
        correct: 1
    },

    {
        question: "XSS nedir?",
        options: ["Bir SQL saldırısıdır", "Bir şifreleme yöntemi", "Cross-Site Scripting", "Bir ağ protokolüdür"],
        correct: 2
    },
    {
        question: "SQL Injection nedir?",
        options: ["Bir veri tabanı saldırısıdır", "Bir güvenlik protokolüdür", "Bir şifreleme yöntemi", "Bir ağ saldırısıdır"],
        correct: 0
    },
    {
        question: "Firewall nedir?",
        options: ["Bir virüs", "Bir yazılım", "Bir donanım", "Bir güvenlik duvarı"],
        correct: 3
    },
    {
        question: "DDoS saldırısı nedir?",
        options: ["Bir virüs", "Bir ağ saldırısı", "Bir güvenlik protokolü", "Bir yazılım"],
        correct: 1
    },
    {
        question: "VPN nedir?",
        options: ["Bir ağ cihazı", "Bir yazılım", "Bir sanal özel ağ", "Bir veri tabanı"],
        correct: 2
    },

    {
        question: "HTML nedir?",
        options: ["Bir programlama dili", "Bir işaretleme dili", "Bir veritabanı", "Bir stil dosyası"],
        correct: 1
    },
    {
        question: "CSS nedir?",
        options: ["Bir programlama dili", "Bir veri tabanı", "Bir işaretleme dili", "Bir stil dosyası"],
        correct: 3
    },
    {
        question: "JavaScript nedir?",
        options: ["Bir stil dosyası", "Bir işaretleme dili", "Bir programlama dili", "Bir veri tabanı"],
        correct: 2
    },
    {
        question: "HTML elementleri nelerdir?",
        options: ["Tasarım bileşenleri", "Yazı stili", "Web sayfası içerikleri", "Sunucu komutları"],
        correct: 2
    },
    {
        question: "CSS'in açılımı nedir?",
        options: ["Cascading Style Sheets", "Control System Software", "Computer Style Sheets", "Cascade Sheet System"],
        correct: 0
    }
];

let currentQuestionIndex = 0;
let score = 0;
let selectedDifficulty = "";


document.getElementById("managePanelBtn").addEventListener("click", function() {
    document.querySelector(".question-list").style.display = "block";
    document.querySelector(".exam-section").style.display = "none";
    renderQuestions();
});


document.getElementById("startExamBtn").addEventListener("click", function() {
    document.querySelector(".question-list").style.display = "none";
    document.querySelector(".exam-section").style.display = "block";
    startExam();
});

function filterByDifficulty() {
    selectedDifficulty = document.getElementById("difficulty").value;
    renderQuestions();
}

function renderQuestions() {
    const container = document.getElementById("questionContainer");
    container.innerHTML = "";
    questions.forEach((question, index) => {
        const questionItem = document.createElement("div");
        questionItem.className = "question-item";
        questionItem.innerHTML = `
            <span>${question.question}</span>
            <button class="edit-btn" onclick="editQuestion(${index})">Düzenle</button>
            <button class="delete-btn" onclick="deleteQuestion(${index})">Sil</button>
        `;
        container.appendChild(questionItem);
    });
}

filteredQuestions.forEach((question, index) => {
    const questionItem = document.createElement("div");
    questionItem.className = "question-item";
    questionItem.innerHTML = `
        <span>${question.question}</span>
        <button class="edit-btn" onclick="editQuestion(${index})">Düzenle</button>
        <button class="delete-btn" onclick="deleteQuestion(${index})">Sil</button>
    `;
    container.appendChild(questionItem);
});


function startExam() {
    score = 0;
    currentQuestionIndex = 0;
    document.getElementById("scoreDisplay").innerText = `Puan: ${score}`;
    shuffleQuestions();
    showQuestion(currentQuestionIndex);
}

function shuffleQuestions() {
    for (let i = questions.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [questions[i], questions[j]] = [questions[j], questions[i]];
    }
}

function showQuestion(index) {
    const container = document.getElementById("examContainer");
    const question = questions[index];
    container.innerHTML = `
        <h2>${question.question}</h2>
        <div class="options">
            ${question.options.map((option, i) => `
                <div class="option" onclick="selectOption(${index}, ${i})">${option}</div>
            `).join('')}
        </div>
    `;
}

function selectOption(questionIndex, selectedOptionIndex) {
    const question = questions[questionIndex];
    const correctOptionIndex = question.correct;
    const options = document.querySelectorAll(".option");

    if (selectedOptionIndex === correctOptionIndex) {
        options[selectedOptionIndex].classList.add("correct");
        score+= 10;
    } else {
        options[selectedOptionIndex].classList.add("wrong");
        options[correctOptionIndex].classList.add("correct");
    }

    document.getElementById("scoreDisplay").innerText = `Puan: ${score}`;

    setTimeout(() => {
        currentQuestionIndex++;
        if (currentQuestionIndex < questions.length) {
            showQuestion(currentQuestionIndex);
        } else {
            alert("Sınav bitti! Toplam puanınız: " + score);
            document.querySelector(".question-list").style.display = "block";
            document.querySelector(".exam-section").style.display = "none";
            window.location.reload(); 
        }
    }, 1000);
}


function filterQuestions() {
    const searchInput = document.getElementById("searchInput").value.toLowerCase();
    const filteredQuestions = questions.filter(question => question.question.toLowerCase().includes(searchInput));
    const container = document.getElementById("questionContainer");
    container.innerHTML = "";
    filteredQuestions.forEach((question, index) => {
        const questionItem = document.createElement("div");
        questionItem.className = "question-item";
        questionItem.innerHTML = `
            <span>${question.question}</span>
            <button class="edit-btn" onclick="editQuestion(${index})">Düzenle</button>
            <button class="delete-btn" onclick="deleteQuestion(${index})">Sil</button>
        `;
        container.appendChild(questionItem);
    });
}

function editQuestion(index) {
    const question = questions[index];
    document.getElementById("editQuestionText").value = question.question;
    document.getElementById("editOption1").value = question.options[0];
    document.getElementById("editOption2").value = question.options[1];
    document.getElementById("editOption3").value = question.options[2];
    document.getElementById("editOption4").value = question.options[3];
    document.querySelector(`input[name="editCorrectOption"][value="${question.correct}"]`).checked = true;
    document.getElementById("editQuestionModal").style.display = "block";
    
    document.getElementById("saveEditQuestionBtn").onclick = function() {
        question.question = document.getElementById("editQuestionText").value;
        question.options[0] = document.getElementById("editOption1").value;
        question.options[1] = document.getElementById("editOption2").value;
        question.options[2] = document.getElementById("editOption3").value;
        question.options[3] = document.getElementById("editOption4").value;
        question.correct = parseInt(document.querySelector('input[name="editCorrectOption"]:checked').value);
        renderQuestions();
        document.getElementById("editQuestionModal").style.display = "none";
    };
}


function deleteQuestion(index) {
    questions.splice(index, 1);
    renderQuestions();
}

// Modal ekleme düzenleme kodları
document.getElementById("addQuestionBtn").addEventListener("click", function() {
    document.getElementById("questionModal").style.display = "block";
});

document.querySelectorAll(".close").forEach((closeBtn) => {
    closeBtn.addEventListener("click", function() {
        closeBtn.closest(".modal").style.display = "none";
    });
});


document.getElementById("saveQuestionBtn").addEventListener("click", function() {
    const questionText = document.getElementById("questionText").value;
    if (questionText) {
        questions.push({
            question: questionText,
            options: ["A", "B", "C", "D"], 
            correct: 0 
        });
        renderQuestions();
        document.getElementById("questionModal").style.display = "none";
        document.getElementById("questionText").value = "";
    }
});


