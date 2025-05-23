# Judge Scoring System - LAMP Stack

A comprehensive scoring system for competitions with admin panel, judge interface, and public scoreboard.

## Features

- **Admin Panel**: Manage judges and participants
- **Judge Interface**: Score participants (1-100 points)
- **Public Scoreboard**: Real-time sorted leaderboard
- **Dockerized LAMP Stack**: Complete development environment
- **Bootstrap UI**: Modern, responsive design
- **Security**: Prepared statements, input validation

## Tech Stack

- **Linux**: Ubuntu-based Docker container
- **Apache**: Web server with mod_rewrite
- **MySQL**: Database with optimized schema
- **PHP 8.2**: Modern syntax with features like:
  - Nullable types and union types
  - Match expressions
  - Named arguments
  - Constructor property promotion
  - Arrow functions
  - Null coalescing operators

## System Requirements

- Linux server
- Docker and Docker Compose
- Ports 8080 and 8081 available

## Quick Start

```bash
# Clone and start
git clone <repository>
cd judge-scoring-system
docker-compose up -d

# Access the application
Admin Panel: http://localhost:8080/admin
Judge Portal: http://localhost:8080/judge
Public Scoreboard: http://localhost:8080/scoreboard
PHPMyAdmin: http://localhost:8081 (Username: root, Password: rootpassword)
```

## Management Script

A convenient shell script is included to manage the Docker environment:

```bash
./judge-system.sh [command]
```

Available commands:
- `start` - Start containers
- `stop` - Stop containers
- `logs` - View container logs
- `rebuild` - Rebuild containers
- `mysql` - Connect to MySQL database
- `info` - Show application information
- `backup` - Backup the database
- `restore` - Restore database from backup file
- `help` - Show help message

## Linux-specific Installation

### Prerequisites
- Linux server running Ubuntu 20.04+ or compatible distribution
- Docker (version 20.10+) and Docker Compose (version 2.0+)
- Git (for cloning the repository)
- 2GB RAM minimum, 4GB recommended

### Installation Steps

1. Update your system packages:
   ```bash
   sudo apt update && sudo apt upgrade -y
   ```

2. Install Docker if not already installed:
   ```bash
   curl -fsSL https://get.docker.com -o get-docker.sh
   sudo sh get-docker.sh
   sudo usermod -aG docker $USER
   ```

3. Install Docker Compose:
   ```bash
   sudo apt install docker-compose -y
   ```

4. Clone and deploy the application:
   ```bash
   git clone <repository-url>
   cd judge-scoring-system
   chmod +x judge-system.sh linux-test.sh
   ./judge-system.sh start
   ```

5. Verify installation:
   ```bash
   ./linux-test.sh
   ```

### Firewall Configuration

If you're using UFW (Uncomplicated Firewall):

```bash
sudo ufw allow 8080/tcp
sudo ufw allow 8081/tcp
sudo ufw reload
```

## Troubleshooting

### Common Issues

1. **Permission Denied Errors**:
   ```bash
   chmod +x judge-system.sh linux-test.sh
   ```

2. **Port Conflicts**:
   Check if ports 8080 and 8081 are already in use:
   ```bash
   sudo netstat -tulpn | grep -E '8080|8081'
   ```
   
   To kill processes using these ports:
   ```bash
   sudo kill -9 $(sudo lsof -t -i:8080)
   sudo kill -9 $(sudo lsof -t -i:8081)
   ```

3. **Docker Service Not Running**:
   ```bash
   sudo systemctl start docker
   sudo systemctl enable docker
   ```

4. **Database Connection Issues**:
   Check MySQL container logs:
   ```bash
   docker logs judge-scoring-system-db
   ```

### Testing Script

The included `linux-test.sh` script performs system checks and verifies:
- Docker service status
- Port availability
- Container health
- Database connectivity
- Web server response

## Database Schema

### Core Tables

1. **judges**: Judge management
2. **users**: Participants/competitors
3. **scores**: Judge scoring records
4. **events**: Competition events (future enhancement)

### Key Relationships

- One Judge → Many Scores
- One User → Many Scores (from different judges)
- Composite unique constraint prevents duplicate judge-user scoring

## Assumptions

1. **Authentication**: Simplified for demo (production would use secure sessions)
2. **Single Event**: System handles one competition at a time
3. **Score Range**: 1-100 points per judge per participant
4. **Real-time**: Periodic refresh vs WebSocket implementation
5. **Pre-registration**: Participants are manually added by admins

## Features to Add (Given More Time)

### Priority 1
- JWT-based authentication
- Multi-event support
- Score validation rules
- Audit logging

### Priority 2
- WebSocket real-time updates
- Advanced reporting (CSV/PDF export)
- Email notifications
- Mobile-responsive improvements

### Priority 3
- Analytics dashboard
- API rate limiting
- Automated backups
- Performance monitoring

## Development

```bash
# View logs
docker-compose logs -f

# Access MySQL
docker-compose exec db mysql -u root -p

# PHP debugging
docker-compose exec web tail -f /var/log/apache2/error.log
```
