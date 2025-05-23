#!/bin/bash

# Judge Scoring System - Docker Management Script
# This script provides commands to manage the Docker environment for the judge scoring system

# Color definitions
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Print header
echo -e "${BLUE}=================================${NC}"
echo -e "${BLUE}Judge Scoring System - Docker CLI${NC}"
echo -e "${BLUE}=================================${NC}"

# Function to start the containers
start() {
    echo -e "\n${GREEN}Starting Judge Scoring System containers...${NC}"
    docker-compose up -d
    
    echo -e "\n${GREEN}Checking container status:${NC}"
    docker-compose ps
    
    echo -e "\n${YELLOW}The application is now running at:${NC}"
    echo -e "- Scoreboard: ${BLUE}http://localhost:8080/scoreboard${NC}"
    echo -e "- Judge Portal: ${BLUE}http://localhost:8080/judge${NC}"
    echo -e "- Admin Panel: ${BLUE}http://localhost:8080/admin${NC}"
    echo -e "- PHPMyAdmin: ${BLUE}http://localhost:8081${NC} (Username: root, Password: rootpassword)"
}

# Function to stop the containers
stop() {
    echo -e "\n${YELLOW}Stopping Judge Scoring System containers...${NC}"
    docker-compose down
    echo -e "${GREEN}Containers stopped successfully.${NC}"
}

# Function to view container logs
logs() {
    echo -e "\n${GREEN}Showing logs (press Ctrl+C to exit)...${NC}"
    docker-compose logs -f
}

# Function to rebuild containers
rebuild() {
    echo -e "\n${YELLOW}Rebuilding Judge Scoring System containers...${NC}"
    docker-compose down
    docker-compose build --no-cache
    docker-compose up -d
    echo -e "${GREEN}Containers rebuilt successfully.${NC}"
}

# Function to access MySQL database
mysql() {
    echo -e "\n${GREEN}Connecting to MySQL database...${NC}"
    docker-compose exec db mysql -u root -prootpassword judge_scoring
}

# Function to show application information
info() {
    echo -e "\n${BLUE}Judge Scoring System Information${NC}"
    echo -e "${YELLOW}Container Status:${NC}"
    docker-compose ps
    
    echo -e "\n${YELLOW}Application URLs:${NC}"
    echo -e "- Scoreboard: ${BLUE}http://localhost:8080/scoreboard${NC}"
    echo -e "- Judge Portal: ${BLUE}http://localhost:8080/judge${NC}"
    echo -e "- Admin Panel: ${BLUE}http://localhost:8080/admin${NC}"
    echo -e "- PHPMyAdmin: ${BLUE}http://localhost:8081${NC}"
    
    echo -e "\n${YELLOW}Default Credentials:${NC}"
    echo -e "- MySQL/PHPMyAdmin Username: ${BLUE}root${NC}"
    echo -e "- MySQL/PHPMyAdmin Password: ${BLUE}rootpassword${NC}"
    
    echo -e "\n${YELLOW}Project Structure:${NC}"
    echo -e "- Source Code: ${BLUE}./src/${NC}"
    echo -e "- Database Init SQL: ${BLUE}./database/init.sql${NC}"
    echo -e "- Apache Config: ${BLUE}./config/apache/000-default.conf${NC}"
}

# Function to backup database
backup() {
    timestamp=$(date +"%Y%m%d_%H%M%S")
    backup_file="judge_scoring_backup_$timestamp.sql"
    
    echo -e "\n${GREEN}Creating database backup...${NC}"
    docker-compose exec db mysqldump -u root -prootpassword judge_scoring > "./backups/$backup_file"
    
    if [ $? -eq 0 ]; then
        echo -e "${GREEN}Backup created successfully:${NC} ./backups/$backup_file"
    else
        echo -e "${RED}Error creating backup.${NC}"
    fi
}

# Function to restore database
restore() {
    if [ -z "$1" ]; then
        echo -e "${RED}Error: Please provide a backup file to restore.${NC}"
        echo -e "Usage: $0 restore [backup_file]"
        return 1
    fi
    
    if [ ! -f "$1" ]; then
        echo -e "${RED}Error: Backup file not found.${NC}"
        return 1
    fi
    
    echo -e "\n${YELLOW}WARNING: This will overwrite the current database. Continue? (y/n)${NC}"
    read -r confirm
    
    if [ "$confirm" = "y" ] || [ "$confirm" = "Y" ]; then
        echo -e "\n${GREEN}Restoring database from backup...${NC}"
        docker-compose exec -T db mysql -u root -prootpassword judge_scoring < "$1"
        
        if [ $? -eq 0 ]; then
            echo -e "${GREEN}Database restored successfully.${NC}"
        else
            echo -e "${RED}Error restoring database.${NC}"
        fi
    else
        echo -e "${YELLOW}Restore cancelled.${NC}"
    fi
}

# Function to print help
help() {
    echo -e "\n${YELLOW}Usage:${NC} $0 [command]"
    echo -e "\n${YELLOW}Available commands:${NC}"
    echo -e "  ${BLUE}start${NC}     - Start containers"
    echo -e "  ${BLUE}stop${NC}      - Stop containers"
    echo -e "  ${BLUE}logs${NC}      - View container logs"
    echo -e "  ${BLUE}rebuild${NC}   - Rebuild containers"
    echo -e "  ${BLUE}mysql${NC}     - Connect to MySQL database"
    echo -e "  ${BLUE}info${NC}      - Show application information"
    echo -e "  ${BLUE}backup${NC}    - Backup the database"
    echo -e "  ${BLUE}restore${NC}   - Restore database from backup file"
    echo -e "  ${BLUE}help${NC}      - Show this help message"
}

# Create backups directory if it doesn't exist
mkdir -p backups

# Parse command
case "$1" in
    start)
        start
        ;;
    stop)
        stop
        ;;
    logs)
        logs
        ;;
    rebuild)
        rebuild
        ;;
    mysql)
        mysql
        ;;
    info)
        info
        ;;
    backup)
        backup
        ;;
    restore)
        restore "$2"
        ;;
    help|"")
        help
        ;;
    *)
        echo -e "\n${RED}Error: Unknown command '$1'${NC}"
        help
        exit 1
        ;;
esac

exit 0
