package main

import (
	"bufio"
	"encoding/json"
	"fmt"
	"os"
	"time"
)

type User struct {
	Username string `json:"username"`
	Password string `json:"password"`
	Role     string `json:"role"`
}

func loadUsers() ([]User, error) {
	file, err := os.Open("users.json")
	if err != nil {
		defaultUsers := []User{
			{Username: "admin", Password: "admin", Role: "admin"},
		}
		saveUsers(defaultUsers)
		return defaultUsers, nil
	}
	defer file.Close()

	var users []User
	err = json.NewDecoder(file).Decode(&users)
	return users, err
}

func saveUsers(users []User) error {
	file, err := os.Create("users.json")
	if err != nil {
		return err
	}
	defer file.Close()

	return json.NewEncoder(file).Encode(users)
}

func logToFile(message string) {
	file, err := os.OpenFile("log.txt", os.O_APPEND|os.O_CREATE|os.O_WRONLY, 0644)
	if err != nil {
		fmt.Println("Log dosyasına yazılamadı:", err)
		return
	}
	defer file.Close()
	logMessage := fmt.Sprintf("%s - %s\n", time.Now().Format("2006-01-02 15:04:05"), message)
	file.WriteString(logMessage)
}

func login(username, password string, users []User) (User, bool) {
	for _, user := range users {
		if user.Username == username && user.Password == password {
			logToFile(fmt.Sprintf("Başarılı giriş: %s", username))
			return user, true
		}
	}
	logToFile(fmt.Sprintf("Başarısız giriş: %s", username))
	return User{}, false
}

func adminActions(users *[]User) {
	scanner := bufio.NewScanner(os.Stdin)
	for {
		fmt.Println("\nAdmin Menü:")
		fmt.Println("1. Müşteri Ekleme")
		fmt.Println("2. Müşteri Silme")
		fmt.Println("3. Log Listeleme")
		fmt.Println("0. Çıkış")

		fmt.Print("Seçiminiz: ")
		scanner.Scan()
		choice := scanner.Text()

		switch choice {
		case "1":
			addCustomer(users)
		case "2":
			deleteCustomer(users)
		case "3":
			showLogs()
		case "0":
			return
		default:
			fmt.Println("Geçersiz seçim.")
		}
	}
}

func customerActions(user User) {
	scanner := bufio.NewScanner(os.Stdin)
	for {
		fmt.Println("\nMüşteri Menü:")
		fmt.Println("1. Profil Görüntüleme")
		fmt.Println("2. Şifre Değiştirme")
		fmt.Println("0. Çıkış")

		fmt.Print("Seçiminiz: ")
		scanner.Scan()
		choice := scanner.Text()

		switch choice {
		case "1":
			viewProfile(user)
		case "2":
			changePassword(&user)
		case "0":
			return
		default:
			fmt.Println("Geçersiz seçim.")
		}
	}
}

func addCustomer(users *[]User) {
	scanner := bufio.NewScanner(os.Stdin)
	fmt.Print("Yeni müşteri kullanıcı adı: ")
	scanner.Scan()
	username := scanner.Text()

	fmt.Print("Yeni müşteri şifresi: ")
	scanner.Scan()
	password := scanner.Text()

	*users = append(*users, User{username, password, "customer"})
	logToFile(fmt.Sprintf("Müşteri eklendi: %s", username))
	fmt.Println("Müşteri başarıyla eklendi.")

	err := saveUsers(*users)
	if err != nil {
		fmt.Println("Kullanıcıları kaydetme hatası:", err)
	}
}

func deleteCustomer(users *[]User) {
	scanner := bufio.NewScanner(os.Stdin)
	fmt.Print("Silinecek müşteri kullanıcı adı: ")
	scanner.Scan()
	username := scanner.Text()

	for i, user := range *users {
		if user.Username == username && user.Role == "customer" {
			*users = append((*users)[:i], (*users)[i+1:]...)
			logToFile(fmt.Sprintf("Müşteri silindi: %s", username))
			fmt.Println("Müşteri başarıyla silindi.")

			err := saveUsers(*users)
			if err != nil {
				fmt.Println("Kullanıcıları kaydetme hatası:", err)
			}
			return
		}
	}
	fmt.Println("Müşteri bulunamadı.")
}

func showLogs() {
	file, err := os.Open("log.txt")
	if err != nil {
		fmt.Println("Log dosyası açılamadı:", err)
		return
	}
	defer file.Close()

	fmt.Println("\nLoglar:")
	scanner := bufio.NewScanner(file)
	for scanner.Scan() {
		fmt.Println(scanner.Text())
	}
}

func viewProfile(user User) {
	fmt.Printf("\nProfil: %s\n", user.Username)
}

func changePassword(user *User) {
	scanner := bufio.NewScanner(os.Stdin)
	fmt.Print("Yeni şifreniz: ")
	scanner.Scan()
	newPassword := scanner.Text()
	user.Password = newPassword
	logToFile(fmt.Sprintf("Şifre değiştirildi: %s", user.Username))
	fmt.Println("Şifre başarıyla değiştirildi.")
}

func main() {
	users, err := loadUsers()
	if err != nil {
		fmt.Println("Kullanıcıları yükleme hatası:", err)
		return
	}

	scanner := bufio.NewScanner(os.Stdin)
	fmt.Println("0 - Admin Girişi, 1 - Müşteri Girişi")

	for {
		fmt.Print("Seçiminiz: ")
		scanner.Scan()
		userType := scanner.Text()

		if userType != "0" && userType != "1" {
			fmt.Println("Geçersiz seçim. Tekrar deneyin.")
			continue
		}

		fmt.Print("Kullanıcı adı: ")
		scanner.Scan()
		username := scanner.Text()

		fmt.Print("Şifre: ")
		scanner.Scan()
		password := scanner.Text()

		user, success := login(username, password, users)
		if success {
			if userType == "0" && user.Role == "admin" {
				fmt.Println("\nAdmin olarak giriş yaptınız.")
				adminActions(&users)
			} else if userType == "1" && user.Role == "customer" {
				fmt.Println("\nMüşteri olarak giriş yaptınız.")
				customerActions(user)
			} else {
				fmt.Println("Kullanıcı türü yetkili değil.")
			}
		} else {
			fmt.Println("Giriş başarısız. Tekrar deneyin.")
		}
	}
}
