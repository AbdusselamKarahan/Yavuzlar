package main

import (
	"fmt"
	"net/http"
	"os"
	"strings"

	"github.com/PuerkitoBio/goquery"
)

const (
	Red    = "\033[31m"
	Green  = "\033[32m"
	Yellow = "\033[33m"
	Blue   = "\033[34m"
	Cyan   = "\033[36m"
	White  = "\033[37m"
	Reset  = "\033[0m"
)

func main() {
	var command string

	fmt.Println(Cyan + "========== WEB SCRAPER CLI ==========" + Reset)
	fmt.Println(Green + "1. Scrape data from  The Hacker News --> " + Yellow + "-1" + Reset)
	fmt.Println(Green + "2. Scrape data from  TechnoPat        --> " + Yellow + "-2" + Reset)
	fmt.Println(Green + "3. Scrape data from  Maçkolik         --> " + Yellow + "-3" + Reset)
	fmt.Println(Green + "4. Exit                              --> " + Yellow + "-4" + Reset)
	fmt.Print(Blue + "Enter your command: " + Reset)
	fmt.Scanln(&command)

	switch command {
	case "-1":
		TheHackerNews()
	case "-2":
		TechnoPat()
	case "-3":
		Maçkolik()
	case "-4":
		fmt.Println(Yellow + "Exiting... Goodbye!" + Reset)
		return
	default:
		fmt.Println(Red + "Invalid input, please try again." + Reset)
	}
}

func createTxtFolder() {
	if _, err := os.Stat("Txt"); os.IsNotExist(err) {
		err := os.Mkdir("Txt", 0755)
		if err != nil {
			fmt.Println(Red + "Error creating Txt folder: " + err.Error() + Reset)
			return
		}
		fmt.Println(Green + "Txt folder created successfully." + Reset)
	}
}

func saveToFile(filename, content string) {
	filePath := fmt.Sprintf("Txt/%s.txt", filename)
	file, err := os.OpenFile(filePath, os.O_CREATE|os.O_WRONLY|os.O_TRUNC, 0644)
	if err != nil {
		fmt.Println(Red + "Error writing to file: " + err.Error() + Reset)
		return
	}
	defer file.Close()

	_, err = file.WriteString(content)
	if err != nil {
		fmt.Println(Red + "Error saving content: " + err.Error() + Reset)
	}
}

func TheHackerNews() {
	createTxtFolder()
	fmt.Println(Cyan + "Scraping data from The Hacker News..." + Reset)
	res, err := http.Get("https://thehackernews.com/")
	if err != nil || res.StatusCode != 200 {
		fmt.Println(Red + "Error fetching The Hacker News: " + err.Error() + Reset)
		return
	}
	defer res.Body.Close()

	doc, err := goquery.NewDocumentFromReader(res.Body)
	if err != nil {
		fmt.Println(Red + "Error parsing document: " + err.Error() + Reset)
		return
	}

	var output string
	doc.Find(".body-post.clear .clear.home-right").Each(func(i int, selection *goquery.Selection) {
		title := strings.TrimSpace(selection.Find(".home-title").Text())
		content := strings.TrimSpace(selection.Find(".home-desc").Text())
		date := strings.TrimSpace(selection.Find(".h-datetime").Text())

		entry := fmt.Sprintf("Title: %s\nDate: %s\nContent: %s\n\n", title, date, content)
		output += entry

		fmt.Println(Green + fmt.Sprintf("%d: %s", i+1, title) + Reset)
		fmt.Println(Yellow + "Content Date: " + date + Reset)
		fmt.Println(White + content + Reset)
		fmt.Println(Cyan + "***********************************************************************" + Reset)
	})

	saveToFile("TheHackerNews", output)
}

func TechnoPat() {
	createTxtFolder()
	fmt.Println(Cyan + "Scraping data from TechnoPat..." + Reset)
	res, err := http.Get("https://www.technopat.net")
	if err != nil || res.StatusCode != 200 {
		fmt.Println(Red + "Error fetching TechnoPat: " + err.Error() + Reset)
		return
	}
	defer res.Body.Close()

	doc, err := goquery.NewDocumentFromReader(res.Body)
	if err != nil {
		fmt.Println(Red + "Error parsing document: " + err.Error() + Reset)
		return
	}

	var output string
	doc.Find(".td_module_14").Each(func(i int, selection *goquery.Selection) {
		title := strings.TrimSpace(selection.Find("h3").Text())
		content := strings.TrimSpace(selection.Find(".td-excerpt").Text())
		date := strings.TrimSpace(selection.Find(".td-post-date").Text())

		entry := fmt.Sprintf("Title: %s\nDate: %s\nContent: %s\n\n", title, date, content)
		output += entry

		fmt.Println(Green + fmt.Sprintf("%d: %s", i+1, title) + Reset)
		fmt.Println(Yellow + "Content Date: " + date + Reset)
		fmt.Println(White + content + Reset)
		fmt.Println(Cyan + "***********************************************************************" + Reset)
	})

	saveToFile("TechnoPat", output)
}

func Maçkolik() {
	createTxtFolder()
	fmt.Println(Cyan + "Scraping data from Maçkolik..." + Reset)
	res, err := http.Get("https://arsiv.mackolik.com/News/")
	if err != nil || res.StatusCode != 200 {
		fmt.Println(Red + "Error fetching Maçkolik: " + err.Error() + Reset)
		return
	}
	defer res.Body.Close()

	doc, err := goquery.NewDocumentFromReader(res.Body)
	if err != nil {
		fmt.Println(Red + "Error parsing document: " + err.Error() + Reset)
		return
	}

	var output string
	doc.Find(".news-coll-temp").Each(func(i int, selection *goquery.Selection) {
		title := strings.TrimSpace(selection.Find(".news-coll-img").Text())
		content := strings.TrimSpace(selection.Find(".news-coll-text").Text())
		date := strings.TrimSpace(selection.Find(".news-coll-date").Text())

		entry := fmt.Sprintf("Title: %s\nDate: %s\nContent: %s\n\n", title, date, content)
		output += entry

		fmt.Println(Green + fmt.Sprintf("%d: %s", i+1, title) + Reset)
		fmt.Println(Yellow + "Content Date: " + date + Reset)
		fmt.Println(White + content + Reset)
		fmt.Println(Cyan + "***********************************************************************" + Reset)
	})

	saveToFile("Maçkolik", output)
}
