#!/bin/bash

# Judge Scoring System - Linux Server Test Script
# This script helps test that the application is running correctly

# Color definitions
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Print header
echo -e "${BLUE}=====================================${NC}"
echo -e "${BLUE}Judge Scoring System - Linux Test CLI${NC}"
echo -e "${BLUE}=====================================${NC}"

# Check if Docker is installed
check_docker() {
    echo -e "\n${YELLOW}Checking Docker installation...${NC}"
    if command -v docker &> /dev/null; then
        echo -e "${GREEN}✓ Docker is installed${NC}"
        docker --version
    else
        echo -e "${RED}✗ Docker is not installed${NC}"
        echo -e "${YELLOW}Please install Docker using:${NC}"
        echo -e "sudo apt-get update && sudo apt-get install -y docker.io"
        return 1
    fi
    
    # Check Docker Compose
    if command -v docker-compose &> /dev/null; then
        echo -e "${GREEN}✓ Docker Compose is installed${NC}"
        docker-compose --version
    else
        echo -e "${RED}✗ Docker Compose is not installed${NC}"
        echo -e "${YELLOW}Please install Docker Compose using:${NC}"
        echo -e "sudo curl -L \"https://github.com/docker/compose/releases/download/1.29.2/docker-compose-\$(uname -s)-\$(uname -m)\" -o /usr/local/bin/docker-compose && sudo chmod +x /usr/local/bin/docker-compose"
        return 1
    fi
    
    return 0
}

# Check if the ports are available
check_ports() {
    echo -e "\n${YELLOW}Checking if required ports are available...${NC}"
    
    # Check port 8080
    if netstat -tuln | grep -q ":8080 "; then
        echo -e "${RED}✗ Port 8080 is already in use${NC}"
        echo -e "${YELLOW}Please free port 8080 or modify docker-compose.yml to use a different port${NC}"
        return 1
    else
        echo -e "${GREEN}✓ Port 8080 is available${NC}"
    fi
    
    # Check port 8081
    if netstat -tuln | grep -q ":8081 "; then
        echo -e "${RED}✗ Port 8081 is already in use${NC}"
        echo -e "${YELLOW}Please free port 8081 or modify docker-compose.yml to use a different port${NC}"
        return 1
    else
        echo -e "${GREEN}✓ Port 8081 is available${NC}"
    fi
    
    return 0
}

# Test if containers are running
test_containers() {
    echo -e "\n${YELLOW}Testing if containers are running...${NC}"
    
    # Check if containers are running
    if [ "$(docker-compose ps --services --filter "status=running" | wc -l)" -lt 2 ]; then
        echo -e "${RED}✗ Not all containers are running${NC}"
        echo -e "${YELLOW}Current status:${NC}"
        docker-compose ps
        return 1
    else
        echo -e "${GREEN}✓ All containers are running${NC}"
        return 0
    fi
}

# Test HTTP connections
test_http() {
    echo -e "\n${YELLOW}Testing HTTP connections...${NC}"
    
    # Check main application
    if curl -s --head http://localhost:8080/scoreboard | grep "200 OK" > /dev/null; then
        echo -e "${GREEN}✓ Scoreboard is accessible${NC}"
    else
        echo -e "${RED}✗ Cannot access Scoreboard${NC}"
        return 1
    fi
    
    # Check judge portal
    if curl -s --head http://localhost:8080/judge | grep "200 OK" > /dev/null; then
        echo -e "${GREEN}✓ Judge Portal is accessible${NC}"
    else
        echo -e "${RED}✗ Cannot access Judge Portal${NC}"
        return 1
    fi
    
    # Check admin panel
    if curl -s --head http://localhost:8080/admin | grep "200 OK" > /dev/null; then
        echo -e "${GREEN}✓ Admin Panel is accessible${NC}"
    else
        echo -e "${RED}✗ Cannot access Admin Panel${NC}"
        return 1
    fi
    
    # Check PHPMyAdmin
    if curl -s --head http://localhost:8081 | grep "200 OK" > /dev/null; then
        echo -e "${GREEN}✓ PHPMyAdmin is accessible${NC}"
    else
        echo -e "${RED}✗ Cannot access PHPMyAdmin${NC}"
        return 1
    fi
    
    return 0
}

# Test database connection
test_database() {
    echo -e "\n${YELLOW}Testing database connection...${NC}"
    
    # Try to connect to MySQL and run a simple query
    if docker-compose exec -T db mysql -u root -prootpassword -e "USE judge_scoring; SELECT COUNT(*) FROM users;" > /dev/null 2>&1; then
        echo -e "${GREEN}✓ Database connection successful${NC}"
        
        # Get some basic stats
        echo -e "\n${BLUE}Database Statistics:${NC}"
        echo -e "${YELLOW}Users:${NC} $(docker-compose exec -T db mysql -u root -prootpassword -e "USE judge_scoring; SELECT COUNT(*) FROM users;" -s)"
        echo -e "${YELLOW}Judges:${NC} $(docker-compose exec -T db mysql -u root -prootpassword -e "USE judge_scoring; SELECT COUNT(*) FROM judges;" -s)"
        echo -e "${YELLOW}Scores:${NC} $(docker-compose exec -T db mysql -u root -prootpassword -e "USE judge_scoring; SELECT COUNT(*) FROM scores;" -s)"
        
        return 0
    else
        echo -e "${RED}✗ Database connection failed${NC}"
        echo -e "${YELLOW}Please check MySQL container logs using:${NC}"
        echo -e "docker-compose logs db"
        return 1
    fi
}

# Run all tests
run_all_tests() {
    echo -e "\n${BLUE}Running all system tests...${NC}"
    
    # Check Docker
    if ! check_docker; then
        return 1
    fi
    
    # Check ports
    if ! check_ports; then
        return 1
    fi
    
    # Test containers
    if ! test_containers; then
        return 1
    fi
    
    # Test HTTP connections
    if ! test_http; then
        return 1
    fi
    
    # Test database
    if ! test_database; then
        return 1
    fi
    
    echo -e "\n${GREEN}All tests passed successfully!${NC}"
    echo -e "${GREEN}The Judge Scoring System is correctly installed and running.${NC}"
    
    return 0
}

# Parse command
case "$1" in
    docker)
        check_docker
        ;;
    ports)
        check_ports
        ;;
    containers)
        test_containers
        ;;
    http)
        test_http
        ;;
    database)
        test_database
        ;;
    all|"")
        run_all_tests
        ;;
    *)
        echo -e "\n${RED}Error: Unknown command '$1'${NC}"
        echo -e "\n${YELLOW}Usage:${NC} $0 [command]"
        echo -e "\n${YELLOW}Available commands:${NC}"
        echo -e "  ${BLUE}docker${NC}     - Check Docker installation"
        echo -e "  ${BLUE}ports${NC}      - Check if required ports are available"
        echo -e "  ${BLUE}containers${NC} - Test if containers are running"
        echo -e "  ${BLUE}http${NC}       - Test HTTP connections"
        echo -e "  ${BLUE}database${NC}   - Test database connection"
        echo -e "  ${BLUE}all${NC}        - Run all tests (default)"
        exit 1
        ;;
esac

exit 0
