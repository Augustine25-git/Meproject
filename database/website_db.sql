-- Sprint 7: Database Creation for Personal Website
-- Create database and tables to store website content

-- Create the database
CREATE DATABASE IF NOT EXISTS personal_website_db;
USE personal_website_db;

-- Users table for sign up/sign in functionality
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    phone VARCHAR(20),
    company VARCHAR(100),
    interests TEXT,
    newsletter_subscription BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Services table to store service offerings
CREATE TABLE services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    service_name VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    duration VARCHAR(50),
    price_range VARCHAR(100),
    category VARCHAR(50),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Service requests table to store form submissions
CREATE TABLE service_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    company VARCHAR(100),
    service_required VARCHAR(100) NOT NULL,
    budget_range VARCHAR(50),
    timeline VARCHAR(50),
    start_date DATE,
    end_date DATE,
    project_description TEXT NOT NULL,
    newsletter_subscription BOOLEAN DEFAULT FALSE,
    status ENUM('pending', 'reviewed', 'approved', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Projects table to store portfolio projects
CREATE TABLE projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    description TEXT NOT NULL,
    technologies TEXT,
    image_url VARCHAR(255),
    project_url VARCHAR(255),
    github_url VARCHAR(255),
    category VARCHAR(50),
    is_featured BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Skills table to store technical and soft skills
CREATE TABLE skills (
    id INT AUTO_INCREMENT PRIMARY KEY,
    skill_name VARCHAR(100) NOT NULL,
    category VARCHAR(50) NOT NULL,
    proficiency_level ENUM('beginner', 'intermediate', 'advanced', 'expert') DEFAULT 'intermediate',
    description TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Education table to store academic background
CREATE TABLE education (
    id INT AUTO_INCREMENT PRIMARY KEY,
    degree VARCHAR(200) NOT NULL,
    institution VARCHAR(200) NOT NULL,
    field_of_study VARCHAR(100),
    graduation_year YEAR,
    gpa DECIMAL(3,2),
    achievements TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Experience table to store work history
CREATE TABLE experience (
    id INT AUTO_INCREMENT PRIMARY KEY,
    job_title VARCHAR(100) NOT NULL,
    company VARCHAR(200) NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE,
    is_current BOOLEAN DEFAULT FALSE,
    description TEXT,
    achievements TEXT,
    technologies_used TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Contact messages table to store contact form submissions
CREATE TABLE contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    subject VARCHAR(200),
    message TEXT NOT NULL,
    status ENUM('unread', 'read', 'replied', 'archived') DEFAULT 'unread',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Blog posts table for content management
CREATE TABLE blog_posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    content TEXT NOT NULL,
    excerpt TEXT,
    author_id INT,
    status ENUM('draft', 'published', 'archived') DEFAULT 'draft',
    published_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Insert sample data for services
INSERT INTO services (service_name, description, duration, price_range, category) VALUES
('Web Development', 'Custom website development using modern technologies like HTML5, CSS3, JavaScript, and PHP', '2-4 weeks', '$1,500 - $5,000', 'Development'),
('E-commerce Solutions', 'Online store development with payment integration and inventory management', '3-6 weeks', '$3,000 - $8,000', 'E-commerce'),
('Mobile App Development', 'Cross-platform mobile applications using React Native and Flutter', '4-8 weeks', '$5,000 - $15,000', 'Mobile'),
('Website Maintenance', 'Regular updates, security patches, and performance optimization', 'Ongoing', '$200 - $500/month', 'Maintenance'),
('SEO Optimization', 'Search engine optimization and digital marketing services', '1-3 months', '$500 - $2,000', 'Marketing'),
('Consulting', 'Technical consulting and project planning services', 'As needed', '$100 - $200/hour', 'Consulting');

-- Insert sample data for skills
INSERT INTO skills (skill_name, category, proficiency_level, description) VALUES
('HTML5 & CSS3', 'Frontend', 'expert', 'Proficient in modern HTML and CSS with responsive design'),
('JavaScript (ES6+)', 'Frontend', 'advanced', 'Strong knowledge of modern JavaScript and frameworks'),
('PHP', 'Backend', 'advanced', 'Server-side development with PHP and MySQL'),
('Python', 'Backend', 'intermediate', 'Data analysis and automation with Python'),
('React.js', 'Frontend', 'advanced', 'Modern React development with hooks and context'),
('MySQL/MariaDB', 'Database', 'advanced', 'Database design and management'),
('Git & GitHub', 'Tools', 'expert', 'Version control and collaboration'),
('Bootstrap', 'Frontend', 'expert', 'Responsive design with Bootstrap framework'),
('Node.js', 'Backend', 'intermediate', 'Server-side JavaScript development'),
('Docker', 'DevOps', 'intermediate', 'Containerization and deployment');

-- Insert sample data for projects
INSERT INTO projects (title, description, technologies, category, is_featured) VALUES
('Personal Website (Sprint 7)', 'A comprehensive personal website with 10+ pages showcasing professional skills and experience. Features modern design with consistent 4-color theme, Bootstrap framework, and JavaScript validation.', 'HTML5, CSS3, Bootstrap, JavaScript', 'Personal', TRUE),
('E-commerce Platform', 'A fully functional e-commerce platform with user authentication, product catalog, shopping cart, and payment integration.', 'PHP, MySQL, JavaScript, Bootstrap', 'E-commerce', TRUE),
('Task Management App', 'A collaborative task management tool with real-time updates, team collaboration features, and progress tracking.', 'React.js, Node.js, MongoDB', 'Web Application', TRUE),
('Database Management System', 'Designed and implemented a comprehensive database management system for a university library.', 'PHP, MySQL, HTML, CSS', 'Database', FALSE),
('Portfolio Website', 'Created a portfolio website showcasing various web development projects and skills.', 'HTML, CSS, JavaScript', 'Personal', FALSE);

-- Insert sample data for education
INSERT INTO education (degree, institution, field_of_study, graduation_year, gpa) VALUES
('Bachelor of Science', 'University of Technology', 'Computer Science', 2023, 3.8),
('High School Diploma', 'Tech High School', 'General Studies', 2019, 3.9);

-- Insert sample data for experience
INSERT INTO experience (job_title, company, start_date, end_date, is_current, description) VALUES
('Web Developer', 'Tech Solutions Inc.', '2023-01-01', NULL, TRUE, 'Full-stack web development with focus on modern web technologies'),
('Junior Developer', 'Digital Creations', '2022-06-01', '2022-12-31', FALSE, 'Front-end development and UI/UX implementation'),
('Intern - Software Development', 'Innovation Labs', '2022-01-01', '2022-05-31', FALSE, 'Learning and contributing to real-world projects');

-- Create indexes for better performance
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_username ON users(username);
CREATE INDEX idx_service_requests_email ON service_requests(email);
CREATE INDEX idx_service_requests_status ON service_requests(status);
CREATE INDEX idx_contact_messages_status ON contact_messages(status);
CREATE INDEX idx_blog_posts_status ON blog_posts(status);
CREATE INDEX idx_projects_featured ON projects(is_featured);
CREATE INDEX idx_skills_category ON skills(category);
CREATE INDEX idx_skills_active ON skills(is_active);

-- Create a view for active services
CREATE VIEW active_services AS
SELECT * FROM services WHERE is_active = TRUE;

-- Create a view for featured projects
CREATE VIEW featured_projects AS
SELECT * FROM projects WHERE is_featured = TRUE ORDER BY created_at DESC;

-- Create a view for user statistics
CREATE VIEW user_stats AS
SELECT 
    COUNT(*) as total_users,
    COUNT(CASE WHEN newsletter_subscription = TRUE THEN 1 END) as newsletter_subscribers,
    COUNT(CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 END) as new_users_30_days
FROM users; 