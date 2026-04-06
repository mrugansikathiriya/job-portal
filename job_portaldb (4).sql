-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 06, 2026 at 12:55 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `job_portaldb`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `aid` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `aname` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`aid`, `uid`, `aname`) VALUES
(1, 1, 'admin123');

-- --------------------------------------------------------

--
-- Table structure for table `application`
--

CREATE TABLE `application` (
  `aid` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `jid` int(11) NOT NULL,
  `sid` int(11) NOT NULL,
  `resume` varchar(255) DEFAULT NULL,
  `status` enum('pending','shortlisted','rejected','interview_scheduled','selected','withdrawn') DEFAULT 'pending',
  `applied_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `score` int(11) NOT NULL,
  `attempt` int(11) DEFAULT 0,
  `interview_date` date DEFAULT NULL,
  `interview_time` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `application`
--

INSERT INTO `application` (`aid`, `uid`, `jid`, `sid`, `resume`, `status`, `applied_at`, `score`, `attempt`, `interview_date`, `interview_time`) VALUES
(1, 3, 43, 14, '1775222633_84_b_mrugansi_kathiriya.pdf', 'interview_scheduled', '2026-04-03 13:23:53', 3, 1, '2026-04-14', '18:00:00'),
(2, 3, 28, 14, '1775222846_84_b_mrugansi_kathiriya.pdf', 'selected', '2026-04-03 13:27:26', 2, 1, NULL, NULL),
(3, 3, 31, 14, '1775222911_84_b_mrugansi_kathiriya.pdf', 'pending', '2026-04-03 13:28:31', 7, 1, NULL, NULL),
(4, 3, 36, 14, '1775222983_84_b_mrugansi_kathiriya.pdf', 'rejected', '2026-04-03 13:29:43', 0, 0, NULL, NULL),
(5, 3, 38, 14, '1775223038_84_b_mrugansi_kathiriya.pdf', 'withdrawn', '2026-04-03 13:30:38', 3, 1, NULL, NULL),
(6, 7, 43, 15, '1775223279_Sneha_Chodvadiya___2_.pdf', 'pending', '2026-04-03 13:34:39', 6, 1, NULL, NULL),
(7, 7, 40, 15, '1775223307_Sneha_Chodvadiya___2_.pdf', 'interview_scheduled', '2026-04-03 13:35:07', 6, 1, '2026-04-16', '10:00:00'),
(8, 7, 27, 15, '1775223331_Sneha_Chodvadiya_.pdf', 'pending', '2026-04-03 13:35:31', 0, 0, NULL, NULL),
(9, 8, 42, 16, '1775223671_hetvi_chovatiya_1033_bussiness_etiquette.pdf', 'selected', '2026-04-03 13:41:11', 6, 1, NULL, NULL),
(10, 8, 30, 16, '1775223725_hetvi_chovatiya_1033_bussiness_etiquette.pdf', 'pending', '2026-04-03 13:42:05', 2, 1, NULL, NULL),
(11, 9, 43, 17, '1775223961_isha_khunt.pdf', 'selected', '2026-04-03 13:46:01', 3, 1, NULL, NULL),
(12, 9, 37, 17, '1775224024_isha_khunt.pdf', 'selected', '2026-04-03 13:47:04', 6, 1, NULL, NULL),
(13, 9, 41, 17, '1775224090_isha_khunt.pdf', 'pending', '2026-04-03 13:48:10', 4, 1, NULL, NULL),
(14, 3, 40, 14, '', 'interview_scheduled', '2026-04-03 16:39:13', 4, 1, '2026-04-04', '11:00:00'),
(15, 10, 26, 18, '1775238346_605_Internal_Practical_Exam_March_2026___Question_Paper.pdf', 'selected', '2026-04-03 17:45:46', 4, 1, NULL, NULL),
(16, 3, 44, 14, '', 'pending', '2026-04-04 08:11:01', 0, 0, NULL, NULL),
(17, 3, 42, 14, '', 'pending', '2026-04-04 08:16:20', 0, 0, NULL, NULL),
(18, 3, 41, 14, '1775291469_isha_khunt.pdf', 'pending', '2026-04-04 08:31:09', 6, 1, NULL, NULL),
(19, 3, 37, 14, '1775470613_Sneha_Chodvadiya___2_.pdf', 'pending', '2026-04-06 10:16:53', 0, 0, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `company`
--

CREATE TABLE `company` (
  `cid` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `cname` varchar(150) NOT NULL,
  `logo` varchar(255) NOT NULL,
  `website` varchar(255) NOT NULL,
  `location` varchar(150) NOT NULL,
  `description` text NOT NULL,
  `is_verified` tinyint(1) DEFAULT 0,
  `established_at` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `company`
--

INSERT INTO `company` (`cid`, `uid`, `cname`, `logo`, `website`, `location`, `description`, `is_verified`, `established_at`) VALUES
(16, 2, 'Google', 'company_2_1775219141.jpg', 'https://www.google.com/', 'New Delhi-Delhi', 'Google is a global technology company specializing in Internet-related services and products, including search engines, online advertising, cloud computing, software, and hardware. Known for its innovative culture, Google offers career opportunities in software development, data analytics, AI research, cloud services, product management, and more.', 1, '1998-01-01'),
(17, 4, 'Microsoft', 'company_4_1775219911.jpg', 'https://www.microsoft.com', 'Bangalore-Karnataka', 'Microsoft is a global technology company known for its software products, hardware, and cloud services. It develops the Windows operating system, Microsoft Office suite, Azure cloud platform, and devices like Surface. Microsoft offers career opportunities in software development, cloud computing, AI research, product management, IT operations, and more. The company is recognized for innovation, diversity, and fostering a collaborative work environment.', 1, '1975-11-11'),
(18, 5, 'Infosys', 'company_5_1775220744.jpg', 'https://www.infosys.com', 'Mumbai-Maharashtra', 'Infosys is a global leader in information technology (IT) services and consulting. The company provides services such as software development, digital transformation, cloud computing, artificial intelligence, data analytics, and business outsourcing solutions. It helps organizations worldwide improve their digital capabilities and business performance', 1, '1981-01-01'),
(19, 6, 'Meesho', 'company_6_1775221481.jpg', 'https://www.meesho.com', 'Mysore-Karnataka', 'Meesho is an Indian e-commerce and social commerce platform that connects suppliers, resellers, and customers across India. It allows individuals and small businesses to sell products online through social media platforms like WhatsApp, Facebook, and Instagram.\r\nThe platform offers a wide range of products including fashion, home & kitchen items, beauty products, electronics accessories, and daily essentials. Meesho focuses mainly on affordable products and small-town markets, helping entrepreneurs start online businesses with zero or low investment', 1, '2015-04-20'),
(20, 11, 'Spotify', 'company_11_1775290015.jpg', 'https://www.spotify.com', 'Noida-Uttar Pradesh', 'Spotify is a leading global music streaming platform that provides users with access to millions of songs, podcasts, and audio content from artists around the world. Founded in 2006 and headquartered in Stockholm, Sweden, Spotify revolutionized the music industry by offering a legal and user-friendly way to stream music online.', 1, '2000-01-11');

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `fid` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `rating` int(11) NOT NULL,
  `message` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`fid`, `uid`, `name`, `rating`, `message`, `created_at`) VALUES
(1, 2, 'Google', 4, 'This is very useful and amazing website which  helps me to find candidate according our requirement', '2026-04-03 13:50:16'),
(2, 3, 'mrugansi', 5, 'This jobportal helps me to find jobs that satisfying me ', '2026-04-03 16:36:51');

-- --------------------------------------------------------

--
-- Table structure for table `fraud_reports`
--

CREATE TABLE `fraud_reports` (
  `fr_id` int(11) NOT NULL,
  `details` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `cname` varchar(255) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `fraud_reports`
--

INSERT INTO `fraud_reports` (`fr_id`, `details`, `created_at`, `cname`, `user_id`) VALUES
(1, 'pay', '2026-04-06 09:15:05', 'Microsoft', 9),
(2, 'pay', '2026-04-06 09:31:25', 'Microsoft', 9),
(3, 'pay', '2026-04-06 09:38:44', 'Microsoft', 9);

-- --------------------------------------------------------

--
-- Table structure for table `job`
--

CREATE TABLE `job` (
  `jid` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `cid` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text NOT NULL,
  `location` varchar(150) DEFAULT NULL,
  `salary` varchar(150) DEFAULT NULL,
  `salary_type` enum('fixed','range','negotiable') DEFAULT NULL,
  `experience_required` int(11) DEFAULT 0,
  `skillname` varchar(150) DEFAULT NULL,
  `job_type` enum('full-time','part-time','internship','contract') DEFAULT NULL,
  `work_mode` enum('remote','onsite','hybrid') DEFAULT NULL,
  `deadline` date DEFAULT NULL,
  `status` enum('open','closed') DEFAULT 'open',
  `posted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `vacancy` int(11) DEFAULT NULL,
  `applicant` int(11) DEFAULT NULL,
  `is_approve` enum('pending','approved','rejected') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `job`
--

INSERT INTO `job` (`jid`, `uid`, `cid`, `title`, `description`, `location`, `salary`, `salary_type`, `experience_required`, `skillname`, `job_type`, `work_mode`, `deadline`, `status`, `posted_at`, `vacancy`, `applicant`, `is_approve`) VALUES
(26, 4, 17, 'Software Developer', 'We are looking for a talented Java Developer to join our team. You will design, develop, and maintain high-performance Java applications, ensuring scalable and efficient solutions. Collaborate with cross-functional teams to deliver innovative software products.\r\n\r\nResponsibilities:\r\n\r\nDevelop and maintain Java-based applications.\r\nWrite clean, efficient, and well-documented code.\r\nCollaborate with team members on design, development, and troubleshooting.\r\nIntegrate applications with databases, APIs, and third-party services.\r\nParticipate in code reviews and ensure adherence to best practices.\r\n\r\nRequirements:\r\n\r\nStrong knowledge of Java, Spring, Hibernate, and RESTful APIs.\r\nExperience with SQL/MySQL and database design.\r\nUnderstanding of object-oriented programming (OOP) concepts.\r\nProblem-solving skills and ability to work in a team environment.\r\nFreshers with strong fundamentals are welcome.', 'Bangalore-Karnataka', '20', 'fixed', 1, 'Java,Python,Express.js,Node.js,Spring Boot', 'full-time', 'remote', '2026-04-30', 'open', '2026-04-03 12:41:57', 2, 1, 'approved'),
(27, 4, 17, 'Full Stack Developer', 'We are seeking a skilled Full Stack Developer to design, develop, and maintain web applications from front-end to back-end. The candidate will work on building responsive, scalable, and high-performance applications while collaborating with cross-functional teams.\r\n\r\nResponsibilities:\r\n\r\nDevelop and maintain web applications using front-end and back-end technologies.\r\nWrite clean, efficient, and well-documented code.\r\nCollaborate with designers, developers, and product managers to deliver projects.\r\nIntegrate applications with databases, APIs, and third-party services.\r\nParticipate in code reviews and follow best practices.\r\n\r\nRequirements:\r\n\r\nStrong knowledge of JavaScript, HTML/CSS, React/Angular/Vue, Node.js, PHP, or Java.\r\nExperience with SQL/MySQL, MongoDB, or other databases.\r\nUnderstanding of RESTful APIs, web services, and MVC architecture.\r\nAbility to troubleshoot and optimize code for performance.\r\nFreshers with solid fundamentals in web development are welcome.', 'Bangalore-Karnataka', '', 'negotiable', 1, 'MongoDB,Express.js,Node.js,JavaScript,Django', 'full-time', 'remote', '2026-04-15', 'open', '2026-04-03 12:44:52', 2, 1, 'approved'),
(28, 4, 17, 'Android Developer', 'We are seeking a skilled and passionate Android Developer to join our team. The ideal candidate will be responsible for developing, testing, and maintaining high-quality mobile applications for Android devices. You will collaborate with designers, backend developers, and product teams to create user-friendly and efficient applications.\r\n\r\nThe role involves writing clean and maintainable code, integrating APIs, fixing bugs, and optimizing application performance. Candidates should have a strong understanding of Android development using Java or Kotlin and be familiar with modern development tools and frameworks.', 'Mumbai-Maharashtra', '20', 'fixed', 3, 'Android,Django', 'part-time', 'onsite', '2026-04-05', 'closed', '2026-04-03 12:47:18', 3, 1, 'approved'),
(29, 4, 17, 'iOS Developer', 'We are looking for a talented and detail-oriented iOS Developer to join our team and build high-performance, scalable, and user-friendly mobile applications for Apple devices. The ideal candidate will have a strong passion for mobile technologies and a deep understanding of iOS development standards.\r\n\r\nYou will be responsible for designing and developing advanced applications using Swift and modern iOS frameworks, collaborating with cross-functional teams to define and deliver new features, and ensuring the best possible performance, quality, and responsiveness of applications.\r\n\r\nThe role requires writing clean, maintainable code, integrating APIs, debugging issues, and continuously improving application performance and user experience. You should stay updated with the latest Apple technologies and follow best practices in mobile app development.', 'Pune-Maharashtra', '20', 'range', 0, 'PHP,swift,UI/UX Design,RestAPI', 'internship', 'onsite', '2026-04-10', 'open', '2026-04-03 12:49:02', 1, 0, 'approved'),
(30, 5, 18, 'Game Developer', 'We are seeking a creative and passionate Game Developer to design, develop, and maintain engaging, high-performance games across mobile, web, or desktop platforms. The ideal candidate will have a strong understanding of game mechanics, graphics, and user interaction, along with the ability to turn concepts into immersive gaming experiences. You will work closely with designers, artists, and other developers to build interactive features and deliver visually appealing, user-friendly gameplay.\r\n\r\nIn this role, you will be responsible for writing clean, efficient, and scalable code, integrating graphics, animations, and sound elements, and ensuring smooth performance across different devices. You will also debug issues, optimize gameplay, and continuously improve the overall quality of the game based on testing and user feedback. Experience with game engines like Unity or Unreal Engine and proficiency in programming languages such as C#, C++, or JavaScript are essential.\r\n\r\nThe ideal candidate should have a problem-solving mindset, attention to detail, and a passion for gaming and innovation. Familiarity with version control tools like Git, knowledge of 2D/3D development, and an understanding of emerging technologies such as AR/VR or multiplayer gaming will be considered a plus. If you are excited about creating engaging digital experiences and staying ahead of gaming trends, we encourage you to apply.', 'Pune-Maharashtra', '9', 'fixed', 0, 'UI/UX Design,c#,asp.net,c++,css', 'internship', 'hybrid', '2026-04-30', 'open', '2026-04-03 12:53:34', 2, 1, 'approved'),
(31, 5, 18, 'Database Administrator (DBA)', 'We are looking for a skilled and detail-oriented Database Administrator (DBA) to manage, maintain, and secure our organization’s databases. The ideal candidate will be responsible for ensuring high availability, performance, and reliability of database systems while supporting data-driven applications. You will work closely with developers and IT teams to design and optimize database structures that meet business requirements.\r\n\r\nIn this role, you will handle database installation, configuration, monitoring, and performance tuning. You will be responsible for backup and recovery processes, data security, and troubleshooting database-related issues. Strong knowledge of database management systems such as MySQL, PostgreSQL, or Oracle, along with proficiency in SQL, is essential. Experience with indexing, query optimization, and database design will be highly valuable.\r\n\r\nThe ideal candidate should have strong analytical and problem-solving skills, attention to detail, and a proactive approach to maintaining data integrity and security. Familiarity with cloud databases, automation tools, and disaster recovery planning is a plus. If you are passionate about managing critical data systems and ensuring smooth and secure database operations, we encourage you to apply.', 'Pune-Maharashtra', '10', 'fixed', 3, 'Data Structures,MongoDB,PostgreSQL,mariaDB,MySQL', 'full-time', 'onsite', '2026-04-21', 'open', '2026-04-03 12:54:58', 5, 1, 'approved'),
(32, 5, 18, 'Cloud Engineer', 'We are seeking a skilled and motivated Google Cloud Engineer to design, implement, and manage scalable cloud infrastructure on Google Cloud Platform (GCP). The ideal candidate will be responsible for deploying and maintaining cloud-based applications, ensuring high availability, security, and performance. You will collaborate with development and DevOps teams to build reliable and efficient cloud solutions that support business needs.\r\n\r\nIn this role, you will handle cloud architecture, resource provisioning, monitoring, and optimization of GCP services such as Compute Engine, Cloud Storage, and Kubernetes Engine. You will also manage CI/CD pipelines, automate deployments, and ensure best practices in cloud security and cost optimization. Strong knowledge of cloud networking, IAM, and infrastructure-as-code tools like Terraform is essential.\r\n\r\nThe ideal candidate should have strong problem-solving skills, a solid understanding of cloud computing concepts, and hands-on experience with GCP services. Familiarity with containerization (Docker), microservices architecture, and scripting languages is a plus. If you are passionate about cloud technologies and building scalable systems, we encourage you to apply.', 'Pune-Maharashtra', '30', 'range', 2, 'cloud,AWS,C#,JavaScript,json,Data Structures', 'contract', 'hybrid', '2026-04-29', 'open', '2026-04-03 12:56:56', 2, 0, 'approved'),
(33, 5, 18, 'PHP Developer', 'We are looking for a skilled and motivated PHP Developer to join our team and build dynamic, high-performance web applications. The ideal candidate will be responsible for developing, testing, and maintaining server-side logic while ensuring seamless integration with front-end components. You will work closely with designers, developers, and project managers to deliver scalable and user-friendly solutions.\r\n\r\nIn this role, you will write clean, efficient, and well-documented PHP code, develop and manage databases, and integrate APIs and third-party services. You will also be responsible for debugging issues, optimizing application performance, and ensuring security and data protection. Strong knowledge of PHP, MySQL, and frameworks like Laravel or CodeIgniter is essential, along with a good understanding of HTML, CSS, and JavaScript.\r\n\r\nThe ideal candidate should have strong problem-solving skills, attention to detail, and a passion for web development. Familiarity with version control tools like Git, MVC architecture, and RESTful APIs will be an added advantage. If you are eager to learn, grow, and contribute to innovative web projects, we encourage you to apply.', 'Nagpur-Maharashtra', '4.5', 'range', 0, 'PHP,Python,HTML,CSS,JavaScript,json', 'full-time', 'onsite', '2026-04-25', 'open', '2026-04-03 12:58:23', 4, 0, 'approved'),
(34, 5, 18, 'Web Developer', 'The ideal candidate should have strong problem-solving skills, attention to detail, and a passion for web development. Familiarity with version control tools like Git, MVC architecture, and RESTful APIs will be an added advantage. If you are eager to learn, grow, and contribute to innovative web projects, we encourage you to apply.', 'Nashik-Maharashtra', '9.5', 'fixed', 0, 'HTML,CSS,JavaScript,Spring Boot,bootstrap', 'internship', 'hybrid', '2026-04-06', 'closed', '2026-04-03 13:00:02', 1, 0, 'rejected'),
(36, 6, 19, 'Junior Developer', 'We are looking for a motivated and enthusiastic Junior Developer to join our team and assist in building high-quality software applications. The ideal candidate should have a basic understanding of programming and a strong willingness to learn and grow in a professional development environment. You will work closely with senior developers and contribute to real-world projects.\r\n\r\nIn this role, you will write clean and efficient code, assist in debugging and troubleshooting issues, and help in developing and maintaining web or mobile applications. You will also collaborate with team members to implement new features and improve existing systems. Knowledge of programming languages such as PHP, Java, Python, or JavaScript, along with basic understanding of databases and APIs, is required.\r\n\r\nThe ideal candidate should have good problem-solving skills, attention to detail, and a passion for technology. Familiarity with version control tools like Git and basic development frameworks is a plus. If you are eager to start your career in software development and gain hands-on experience, we encourage you to apply.', 'Nashik-Maharashtra', '', 'negotiable', 1, 'CSS,JavaScript,Django,Data Structures,restAPI', 'part-time', 'remote', '2026-04-28', 'open', '2026-04-03 13:07:18', 2, 1, 'approved'),
(37, 6, 19, 'Backend Developer', 'We are looking for a skilled and detail-oriented Backend Developer to build and maintain the server-side logic of our applications. The ideal candidate will be responsible for developing robust, scalable, and secure backend systems that power web or mobile applications. You will work closely with frontend developers, designers, and other team members to ensure seamless integration and smooth functionality.\r\n\r\nIn this role, you will write clean and efficient code, design and manage databases, and develop APIs for application integration. You will also handle performance optimization, debugging, and security implementation to ensure reliable system operations. Strong knowledge of backend technologies such as PHP, Node.js, Python, or Java, along with experience in working with databases like MySQL or MongoDB, is essential.\r\n\r\nThe ideal candidate should have strong problem-solving skills, attention to detail, and a good understanding of system architecture. Familiarity with RESTful APIs, version control tools like Git, and frameworks such as Laravel, Express, or Django is a plus. If you are passionate about building powerful backend systems and scalable applications, we encourage you to apply.', 'Nashik-Maharashtra', '20', 'range', 3, 'Java,GitHub,Git,PHP,Laravel', 'full-time', 'onsite', '2026-04-30', 'open', '2026-04-03 13:08:37', 2, 2, 'approved'),
(38, 6, 19, 'Django Developer', 'We are looking for a skilled and motivated Django Developer to build and maintain robust, scalable web applications. The ideal candidate will be responsible for developing server-side logic, ensuring high performance and responsiveness, and integrating front-end elements into applications. You will work closely with designers and other developers to deliver efficient and user-friendly solutions.\r\n\r\nIn this role, you will write clean and maintainable Python code using the Django framework, design and manage databases, and develop secure RESTful APIs. You will also handle debugging, performance optimization, and implementation of security best practices. Strong knowledge of Python, Django, and databases like PostgreSQL or MySQL is essential.\r\n\r\nThe ideal candidate should have strong problem-solving skills, attention to detail, and a good understanding of web development principles. Familiarity with front-end technologies, version control tools like Git, and deployment on cloud platforms is a plus. If you are passionate about backend development and building scalable applications, we encourage you to apply.', 'Jaipur-Rajasthan', '50', 'fixed', 0, 'Django,JavaScript,PHP,Python,Data Structures', 'contract', 'hybrid', '2026-04-28', 'open', '2026-04-03 13:09:55', 2, 1, 'approved'),
(39, 2, 16, 'Software Developer', 'We are looking for a motivated Software Developer to join our team. You will design, develop, and maintain software applications, ensuring they are scalable, efficient, and user-friendly. Collaborate with cross-functional teams to deliver innovative solutions.\r\n\r\nResponsibilities:\r\n\r\nDevelop, test, and maintain software applications.\r\nWrite clean, efficient, and well-documented code.\r\nCollaborate with team members on design and implementation.\r\nTroubleshoot and debug software issues.\r\nParticipate in code reviews and ensure adherence to best practices.\r\n\r\nRequirements:\r\n\r\nStrong knowledge of Java, Python, C++, PHP, or related programming languages.\r\nUnderstanding of databases, RESTful APIs, and software development lifecycle (SDLC).\r\nProblem-solving skills and ability to work in a team environment.\r\nFreshers with solid fundamentals are welcome.', 'Mumbai-Maharashtra', '45', 'fixed', 3, 'Spring Boot,Java,Data Structures,PHP', 'full-time', 'remote', '2026-04-06', 'closed', '2026-04-03 13:13:19', 4, 0, 'approved'),
(40, 2, 16, 'Python Developer', 'We are looking for a skilled and passionate Python Developer to build efficient, scalable, and high-performance applications. The ideal candidate will be responsible for developing backend logic, integrating APIs, and working with databases to support various web or software applications. You will collaborate with cross-functional teams to deliver reliable and user-friendly solutions.\r\n\r\nIn this role, you will write clean, maintainable Python code, develop and optimize server-side applications, and troubleshoot issues to improve performance and functionality. You will also work with frameworks such as Django or Flask, handle data processing tasks, and ensure application security and scalability. Strong knowledge of Python, along with experience in databases like MySQL or PostgreSQL, is essential.\r\n\r\nThe ideal candidate should have strong problem-solving skills, attention to detail, and a good understanding of software development principles. Familiarity with RESTful APIs, version control tools like Git, and basic front-end technologies is a plus. If you are enthusiastic about programming and building innovative solutions, we encourage you to apply.', 'Kolkata-West Bengal', '', 'negotiable', 1, 'Python,Django,PHP,c,C++', 'full-time', 'onsite', '2026-04-30', 'open', '2026-04-03 13:14:23', 3, 2, 'approved'),
(41, 2, 16, 'C Developer', 'We are looking for a skilled and detail-oriented C Developer to build efficient, reliable, and high-performance applications. The ideal candidate will be responsible for developing system-level software, writing optimized code, and ensuring smooth functionality across different platforms. You will work closely with other developers to design and implement robust solutions.\r\n\r\nIn this role, you will write clean and efficient C code, debug and troubleshoot complex issues, and optimize application performance. You will also work with data structures, memory management, and low-level system components. Strong understanding of pointers, algorithms, and operating system concepts is essential.\r\n\r\nThe ideal candidate should have strong problem-solving skills, attention to detail, and a solid foundation in programming fundamentals. Familiarity with embedded systems, multithreading, and version control tools like Git is a plus. If you are passionate about low-level programming and building efficient software, we encourage you to apply.', 'Coimbatore-Tamil Nadu', '30', 'fixed', 2, 'c,CodeIgniter,C++', 'full-time', 'onsite', '2026-04-25', 'open', '2026-04-03 13:15:13', 2, 2, 'approved'),
(42, 2, 16, 'AI Engineer', 'We are seeking a talented and innovative AI Engineer to design, develop, and deploy intelligent systems and machine learning models. The ideal candidate will be responsible for building AI-powered solutions that enhance automation, improve decision-making, and deliver data-driven insights. You will work closely with data scientists, developers, and product teams to integrate AI capabilities into real-world applications.\r\n\r\nIn this role, you will develop and train machine learning and deep learning models, preprocess and analyze large datasets, and optimize model performance for accuracy and efficiency. You will also be responsible for deploying models into production environments, integrating APIs, and ensuring scalability and reliability. Strong knowledge of Python, machine learning frameworks such as TensorFlow or PyTorch, and experience with data handling libraries is essential.\r\n\r\nThe ideal candidate should have strong analytical and problem-solving skills, along with a passion for artificial intelligence and innovation. Familiarity with natural language processing (NLP), computer vision, cloud platforms, and MLOps practices is a plus. If you are excited about building intelligent systems and shaping the future with AI technologies, we encourage you to apply.', 'Mumbai-Maharashtra', '48', 'range', 1, 'Machine Learning,Data Structures,Python,php,Django', 'full-time', 'onsite', '2026-04-30', 'open', '2026-04-03 13:16:21', 3, 2, 'approved'),
(43, 2, 16, 'C# Developer', 'We are looking for a skilled and motivated C# Developer to build robust, scalable, and high-performance applications. The ideal candidate will be responsible for developing software solutions using C# and the .NET framework, while ensuring clean architecture and efficient code. You will work closely with cross-functional teams to design, develop, and deploy reliable applications.\r\n\r\nIn this role, you will write clean and maintainable C# code, develop backend services, and integrate APIs and databases. You will also be responsible for debugging issues, optimizing performance, and ensuring application security. Strong knowledge of C#, .NET/.NET Core, and experience with databases such as SQL Server is essential.\r\n\r\nThe ideal candidate should have strong problem-solving skills, attention to detail, and a solid understanding of object-oriented programming concepts. Familiarity with ASP.NET, MVC architecture, and version control tools like Git is a plus. If you are passionate about developing modern applications and working with Microsoft technologies, we encourage you to apply.', 'Jodhpur-Rajasthan', '4.5', 'fixed', 2, 'c#,React,JavaScript,Data Structures', 'part-time', 'hybrid', '2026-04-04', 'closed', '2026-04-03 13:17:24', 2, 3, 'approved'),
(44, 11, 20, 'Security Engineer', 'Spotify is known for its advanced algorithms and data-driven approach, which help deliver a highly personalized user experience. With a presence in over 180 countries, the company continues to innovate in digital audio, including podcasts, audiobooks, and live audio content.', 'Noida-Uttar Pradesh', '5 LPA', 'fixed', 1, 'Data Structures,AWS,php', 'full-time', 'onsite', '2026-04-25', 'open', '2026-04-04 08:08:51', 2, 1, 'approved');

-- --------------------------------------------------------

--
-- Table structure for table `job_offers`
--

CREATE TABLE `job_offers` (
  `oid` int(11) NOT NULL,
  `cid` int(11) NOT NULL,
  `sid` int(11) NOT NULL,
  `jid` int(11) NOT NULL,
  `message` text NOT NULL,
  `status` enum('pending','accepted','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `job_offers`
--

INSERT INTO `job_offers` (`oid`, `cid`, `sid`, `jid`, `message`, `status`, `created_at`) VALUES
(1, 16, 14, 42, 'we are interested by  your resume', 'pending', '2026-04-03 13:22:17'),
(2, 16, 17, 40, 'we are interested by your resume and wanted to offer you job as a pyhton developer', 'pending', '2026-04-03 13:53:22'),
(3, 16, 17, 39, 'related to job', 'pending', '2026-04-03 16:50:56');

-- --------------------------------------------------------

--
-- Table structure for table `job_seeker`
--

CREATE TABLE `job_seeker` (
  `sid` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `sname` varchar(150) NOT NULL,
  `education` varchar(255) NOT NULL,
  `experience` varchar(20) NOT NULL,
  `skillname` varchar(255) NOT NULL,
  `bio` text DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `birthdate` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `job_seeker`
--

INSERT INTO `job_seeker` (`sid`, `uid`, `sname`, `education`, `experience`, `skillname`, `bio`, `profile_image`, `birthdate`) VALUES
(14, 3, 'Mrugansi kathiriya', 'BSC.IT', '0-1 Years', 'PHP,Python,JavaScript,Django,GitHub', 'I have hands-on experience through academic projects, internships, and open-source contributions, demonstrating problem-solving skills and a keen interest in learning new technologies.I am eager to start my professional career in software development and contribute effectively to a dynamic team.', 'user_3_1775219578.jpg', '2005-10-27'),
(15, 7, 'Sneha chodvadiya', 'BCA', '0-1 Years', 'Data Structures,PHP,JavaScript,Java,Flutter', 'A motivated and results-oriented professional with hands-on experience in developing and delivering high-quality solutions. Skilled in problem-solving, collaboration, and adapting to new technologies, with a strong ability to understand business requirements and translate them into efficient outcomes. Experienced in working on real-world projects and contributing to team success in fast-paced environments.\r\n\r\nPossesses a solid foundation in technical and analytical skills, along with a commitment to writing clean, efficient, and reliable work. Adept at learning quickly, handling challenges, and continuously improving performance. Strong communication and teamwork abilities help in collaborating effectively with cross-functional teams.\r\n\r\nPassionate about growth and innovation, always eager to explore new tools and technologies to enhance skills and deliver better results. Seeking opportunities to contribute to meaningful projects, expand knowledge, and build a successful professional career.', 'user_7_1775223254.jpg', '2006-01-05'),
(16, 8, 'Hetvi Chovatiya', 'B.Tech', '1-3 Years', 'Data Structures,Python,PHP,Django,AWS', 'I am a motivated and detail-oriented B.Tech graduate with a solid understanding of core IT concepts including programming, database management, and web development. I have practical experience working with technologies such as PHP, JavaScript, HTML, CSS, and MySQL, and have developed projects that demonstrate my ability to build dynamic and responsive applications.', 'user_8_1775223620.jpg', '2006-08-21'),
(17, 9, 'Isha khunt', 'BCS.IT', '3-5 Years', 'PHP,Data Structures', 'A dedicated and results-driven professional with 2–3 years of experience in software development and application building. Skilled in designing, developing, and maintaining scalable and efficient solutions, with a strong understanding of programming concepts, databases, and API integration. Experienced in working on real-world projects and collaborating with cross-functional teams to deliver high-quality, user-focused applications.\r\n\r\nProficient in modern technologies and frameworks, with the ability to write clean, maintainable, and optimized code. Adept at debugging issues, improving application performance, and ensuring reliability and security. Possesses a solid understanding of development methodologies and is comfortable working in fast-paced environments.\r\n\r\nA quick learner with strong analytical and problem-solving skills, committed to continuous improvement and staying updated with emerging technologies. Passionate about building innovative solutions and contributing effectively to team success while growing professionally.', 'user_9_1775223934.jpg', '2006-08-14'),
(18, 10, 'Het kathiriya', 'B.Tech', '3-5 Years', 'Java,Spring Boot,Machine Learning,Data Structures', 'Currently,I am working in infosys as software developer and try to gain more knowledege aboyt latest technology', 'user_10_1775238146.jpg', '2000-10-12');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `nid` int(11) NOT NULL,
  `uid` int(11) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`nid`, `uid`, `message`, `is_read`, `created_at`) VALUES
(1, 2, 'New seeker profile added. Check it now!', 1, '2026-04-03 12:32:58'),
(2, 3, 'New company Added,Check Now! : Microsoft', 1, '2026-04-03 12:38:31'),
(3, 3, 'Microsoft posted a new job: Software Developer', 1, '2026-04-03 12:41:57'),
(4, 4, 'Company  Microsoft: Your job Software Developer has been Approved by admin.', 1, '2026-04-03 12:42:30'),
(5, 3, 'Microsoft posted a new job: Full Stack Developer', 1, '2026-04-03 12:44:52'),
(6, 4, 'Company  Microsoft: Your job Full Stack Developer has been Approved by admin.', 1, '2026-04-03 12:45:18'),
(7, 3, 'Microsoft posted a new job: Android Developer', 1, '2026-04-03 12:47:18'),
(8, 3, 'Microsoft posted a new job: iOS Developer', 1, '2026-04-03 12:49:02'),
(9, 4, 'Company  Microsoft: Your job iOS Developer has been Approved by admin.', 1, '2026-04-03 12:49:24'),
(10, 4, 'Company  Microsoft: Your job Android Developer has been Approved by admin.', 1, '2026-04-03 12:49:26'),
(11, 3, 'New company Added,Check Now! : Infosys', 1, '2026-04-03 12:52:24'),
(12, 3, 'Infosys posted a new job: Game Developer', 1, '2026-04-03 12:53:34'),
(13, 3, 'Infosys posted a new job: Database Administrator (DBA)', 1, '2026-04-03 12:54:58'),
(14, 3, 'Infosys posted a new job: Cloud Engineer', 1, '2026-04-03 12:56:56'),
(15, 3, 'Infosys posted a new job: PHP Developer', 1, '2026-04-03 12:58:23'),
(16, 5, 'Company  Infosys: Your job Game Developer has been Approved by admin.', 1, '2026-04-03 12:58:32'),
(17, 5, 'Company  Infosys: Your job Cloud Engineer has been Approved by admin.', 1, '2026-04-03 12:58:34'),
(18, 5, 'Company  Infosys: Your job PHP Developer has been Approved by admin.', 1, '2026-04-03 12:58:37'),
(19, 5, 'Company  Infosys: Your job Database Administrator (DBA) has been Approved by admin.', 1, '2026-04-03 12:58:39'),
(20, 3, 'Infosys posted a new job: Web Developer', 1, '2026-04-03 13:00:02'),
(21, 5, 'Company  Infosys: Your job Web Developer has been rejected by admin.', 1, '2026-04-03 13:00:12'),
(22, 3, 'New company Added,Check Now! : Meesho', 1, '2026-04-03 13:04:41'),
(23, 3, 'Meesho posted a new job: Data Scientist', 1, '2026-04-03 13:06:04'),
(24, 3, 'Meesho posted a new job: Junior Developer', 1, '2026-04-03 13:07:18'),
(25, 3, 'Meesho posted a new job: Backend Developer', 1, '2026-04-03 13:08:37'),
(26, 3, 'Meesho posted a new job: Django Developer', 1, '2026-04-03 13:09:55'),
(27, 6, 'Company  Meesho: Your job Django Developer has been Approved by admin.', 1, '2026-04-03 13:10:03'),
(28, 6, 'Company  Meesho: Your job Backend Developer has been Approved by admin.', 1, '2026-04-03 13:10:05'),
(29, 6, 'Company  Meesho: Your job Junior Developer has been Approved by admin.', 1, '2026-04-03 13:10:07'),
(30, 6, 'Company  Meesho: Your job Data Scientist has been Approved by admin.', 1, '2026-04-03 13:10:10'),
(31, 3, 'Google posted a new job: Software Developer', 1, '2026-04-03 13:13:19'),
(32, 3, 'Google posted a new job: Python Developer', 1, '2026-04-03 13:14:23'),
(33, 3, 'Google posted a new job: C Developer', 1, '2026-04-03 13:15:13'),
(34, 3, 'Google posted a new job: AI Engineer', 1, '2026-04-03 13:16:21'),
(35, 3, 'Google posted a new job: C# Developer', 1, '2026-04-03 13:17:24'),
(36, 2, 'Company  Google: Your job C# Developer has been Approved by admin.', 1, '2026-04-03 13:17:40'),
(37, 2, 'Company  Google: Your job AI Engineer has been Approved by admin.', 1, '2026-04-03 13:17:42'),
(38, 2, 'Company  Google: Your job C Developer has been Approved by admin.', 1, '2026-04-03 13:17:45'),
(39, 2, 'Company  Google: Your job Python Developer has been Approved by admin.', 1, '2026-04-03 13:17:47'),
(40, 2, 'Company  Google: Your job Software Developer has been Approved by admin.', 1, '2026-04-03 13:17:50'),
(41, 3, 'Job Offer fromGoogle for Mrugansi kathiriya for AI Engineer', 1, '2026-04-03 13:22:22'),
(42, 2, 'New application from Mrugansi kathiriya for job: C# Developer', 1, '2026-04-03 13:23:53'),
(43, 3, 'Google scheduled interview for Mrugansi kathiriya for C# Developer on 2026-04-14 at 18:00', 1, '2026-04-03 13:26:31'),
(44, 4, 'New application from Mrugansi kathiriya for job: Android Developer', 1, '2026-04-03 13:27:26'),
(45, 5, 'New application from Mrugansi kathiriya for job: Database Administrator (DBA)', 0, '2026-04-03 13:28:31'),
(46, 6, 'New application from Mrugansi kathiriya for job: Junior Developer', 1, '2026-04-03 13:29:43'),
(47, 6, 'New application from Mrugansi kathiriya for job: Django Developer', 1, '2026-04-03 13:30:38'),
(48, 6, 'Mrugansi kathiriya has withdrawn application for Django Developer', 1, '2026-04-03 13:30:54'),
(49, 2, 'New seeker profile added. Check it now!', 1, '2026-04-03 13:34:15'),
(50, 4, 'New seeker profile added. Check it now!', 1, '2026-04-03 13:34:15'),
(51, 5, 'New seeker profile added. Check it now!', 0, '2026-04-03 13:34:15'),
(52, 6, 'New seeker profile added. Check it now!', 1, '2026-04-03 13:34:15'),
(53, 2, 'New application from Sneha chodvadiya for job: C# Developer', 1, '2026-04-03 13:34:39'),
(54, 2, 'New application from Sneha chodvadiya for job: Python Developer', 1, '2026-04-03 13:35:07'),
(55, 4, 'New application from Sneha chodvadiya for job: Full Stack Developer', 1, '2026-04-03 13:35:31'),
(56, 2, 'New seeker profile added. Check it now!', 1, '2026-04-03 13:40:21'),
(57, 4, 'New seeker profile added. Check it now!', 1, '2026-04-03 13:40:21'),
(58, 5, 'New seeker profile added. Check it now!', 0, '2026-04-03 13:40:21'),
(59, 6, 'New seeker profile added. Check it now!', 1, '2026-04-03 13:40:21'),
(60, 2, 'New application from Hetvi Chovatiya for job: AI Engineer', 1, '2026-04-03 13:41:11'),
(61, 5, 'New application from Hetvi Chovatiya for job: Game Developer', 0, '2026-04-03 13:42:05'),
(62, 2, 'New seeker profile added. Check it now!', 1, '2026-04-03 13:45:22'),
(63, 4, 'New seeker profile added. Check it now!', 1, '2026-04-03 13:45:22'),
(64, 5, 'New seeker profile added. Check it now!', 0, '2026-04-03 13:45:22'),
(65, 6, 'New seeker profile added. Check it now!', 1, '2026-04-03 13:45:22'),
(66, 2, 'New application from Isha khunt for job: C# Developer', 1, '2026-04-03 13:46:01'),
(67, 6, 'New application from Isha khunt for job: Backend Developer', 1, '2026-04-03 13:47:04'),
(68, 2, 'New application from Isha khunt for job: C Developer', 1, '2026-04-03 13:48:10'),
(69, 9, 'Job Offer fromGoogle for Isha khunt for Python Developer', 0, '2026-04-03 13:53:29'),
(70, 7, 'Google scheduled interview for Sneha chodvadiya for Python Developer on 2026-04-16 at 10:00', 0, '2026-04-03 13:55:50'),
(71, 9, 'Meesho accepted Isha khunt application for Backend Developer', 0, '2026-04-03 13:59:16'),
(72, 3, 'Meesho company deleted job for Data Scientist', 1, '2026-04-03 14:00:49'),
(73, 7, 'Meesho company deleted job for Data Scientist', 0, '2026-04-03 14:00:50'),
(74, 8, 'Meesho company deleted job for Data Scientist', 1, '2026-04-03 14:00:50'),
(75, 9, 'Meesho company deleted job for Data Scientist', 0, '2026-04-03 14:00:50'),
(76, 2, 'New application from Mrugansi kathiriya for job: Python Developer', 1, '2026-04-03 16:39:14'),
(77, 3, 'Google accepted Mrugansi kathiriya application for Python Developer', 1, '2026-04-03 16:43:20'),
(78, 3, 'Google scheduled interview for Mrugansi kathiriya for Python Developer on 2026-04-04 at 11:00', 1, '2026-04-03 16:43:59'),
(79, 9, 'Job Offer fromGoogle for Isha khunt for Software Developer', 0, '2026-04-03 16:51:02'),
(80, 2, 'New seeker profile added. Check it now!', 1, '2026-04-03 17:42:26'),
(81, 4, 'New seeker profile added. Check it now!', 1, '2026-04-03 17:42:26'),
(82, 5, 'New seeker profile added. Check it now!', 0, '2026-04-03 17:42:26'),
(83, 6, 'New seeker profile added. Check it now!', 0, '2026-04-03 17:42:26'),
(84, 4, 'New application from Het kathiriya for job: Software Developer', 1, '2026-04-03 17:45:46'),
(85, 3, 'Microsoft accepted Mrugansi kathiriya application for Android Developer', 1, '2026-04-03 17:50:04'),
(86, 10, 'Microsoft accepted Het kathiriya application for Software Developer', 0, '2026-04-03 17:50:27'),
(87, 3, 'New company Added,Check Now! : Spotify', 1, '2026-04-04 08:06:55'),
(88, 7, 'New company Added,Check Now! : Spotify', 0, '2026-04-04 08:06:55'),
(89, 8, 'New company Added,Check Now! : Spotify', 0, '2026-04-04 08:06:55'),
(90, 9, 'New company Added,Check Now! : Spotify', 0, '2026-04-04 08:06:55'),
(91, 10, 'New company Added,Check Now! : Spotify', 0, '2026-04-04 08:06:55'),
(92, 3, 'Spotify posted a new job: Security Engineer', 1, '2026-04-04 08:08:51'),
(93, 7, 'Spotify posted a new job: Security Engineer', 0, '2026-04-04 08:08:51'),
(94, 8, 'Spotify posted a new job: Security Engineer', 0, '2026-04-04 08:08:51'),
(95, 9, 'Spotify posted a new job: Security Engineer', 0, '2026-04-04 08:08:51'),
(96, 10, 'Spotify posted a new job: Security Engineer', 0, '2026-04-04 08:08:51'),
(97, 11, 'Company  Spotify: Your job Security Engineer has been Approved by admin.', 1, '2026-04-04 08:09:51'),
(98, 11, 'New application from Mrugansi kathiriya for job: Security Engineer', 0, '2026-04-04 08:11:01'),
(99, 2, 'New application from Mrugansi kathiriya for job: AI Engineer', 1, '2026-04-04 08:16:20'),
(100, 2, 'New application from Mrugansi kathiriya for job: C Developer', 1, '2026-04-04 08:31:09'),
(101, 6, 'New application from Mrugansi kathiriya for job: Backend Developer', 0, '2026-04-06 10:16:53');

-- --------------------------------------------------------

--
-- Table structure for table `saved_candidate`
--

CREATE TABLE `saved_candidate` (
  `scid` int(11) NOT NULL,
  `cid` int(11) NOT NULL,
  `sid` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `saved_candidate`
--

INSERT INTO `saved_candidate` (`scid`, `cid`, `sid`, `created_at`) VALUES
(1, 16, 16, '2026-04-03 13:52:17'),
(2, 16, 14, '2026-04-03 13:52:20'),
(6, 16, 18, '2026-04-03 17:47:30');

-- --------------------------------------------------------

--
-- Table structure for table `saved_job`
--

CREATE TABLE `saved_job` (
  `jid` int(11) NOT NULL,
  `uid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `saved_job`
--

INSERT INTO `saved_job` (`jid`, `uid`) VALUES
(42, 3),
(41, 7),
(38, 7),
(41, 8),
(42, 8),
(36, 8),
(40, 9),
(26, 3),
(42, 2),
(26, 10);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `uid` int(11) NOT NULL,
  `uname` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','company','seeker') NOT NULL,
  `contact` varchar(20) NOT NULL,
  `status` enum('active','blocked') DEFAULT 'active',
  `p_image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_completed` tinyint(1) DEFAULT 0,
  `failed_attempts` int(11) DEFAULT 0,
  `last_failed_attempt` int(11) DEFAULT 0,
  `remember_token` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`uid`, `uname`, `email`, `password`, `role`, `contact`, `status`, `p_image`, `created_at`, `is_completed`, `failed_attempts`, `last_failed_attempt`, `remember_token`) VALUES
(1, 'admin', 'careercraft535@gmail.com', '$2y$10$SoRmVXUDpuo3dQv8V2FSf.wfxKqULXLWumnEl0aQ0PFwJBxT.nKDi', 'admin', '7890654324', 'active', NULL, '2026-04-03 12:13:49', 1, 0, 0, 'd08c19b986f49ee36043a7170fcb59be761edec4c2078bfd7af0d148b6902e74'),
(2, 'Google', '2023040193@vnsgu.ac.in', '$2y$10$0YIRLVkDEzAFThxR9z/gg.ma8faLNfCIUrPViRaC9fGAYW1mhbkBi', 'company', '7890654321', 'active', 'company_2_1775219141.jpg', '2026-04-03 12:21:59', 1, 0, 0, NULL),
(3, 'mrugansi', 'mrugansikathiriya505@gmail.com', '$2y$10$YP4x5.zjv4sXQ.U8ehS/euPECriuuSlkZ4wLLOmtlHJnE6136LGQ2', 'seeker', '6354475270', 'active', NULL, '2026-04-03 12:27:10', 1, 0, 0, NULL),
(4, 'microsoft', '2023040201@vnsgu.ac.in', '$2y$10$NVgbLr4ywAUG6HxpCpla.eA1zuCX3bjTvSxfyhC5r3uRoR/yDQQIu', 'company', '7865432134', 'active', 'company_4_1775219911.jpg', '2026-04-03 12:36:21', 1, 0, 0, NULL),
(5, 'infosys', '2023040146@vnsgu.ac.in', '$2y$10$YUMssz7CpptVh7Kx5uzgSuQL/9YxmIM.1wqsksaBeLLE.Fqi09sKS', 'company', '7890654321', 'active', 'company_5_1775220744.jpg', '2026-04-03 12:50:41', 1, 0, 0, NULL),
(6, 'meesho', '2023040144@vnsgu.ac.in', '$2y$10$pE8t.4UkxeQsm7DQ4ZAjLuGTalA6Gi1/7sVXrnPZt3Epg/ujH.61K', 'company', '9467586798', 'active', 'company_6_1775221481.jpg', '2026-04-03 13:01:13', 1, 0, 0, NULL),
(7, 'sneha', 'snehachodvadiya51@gmail.com', '$2y$10$XcnymumK7LokuKoIS183BO4YEwstlnhdjRUcZOBWBW0BayMMG1oya', 'seeker', '6758923409', 'active', NULL, '2026-04-03 13:31:52', 1, 0, 0, NULL),
(8, 'hetvi', 'hetvichovatiya6@gmail.com', '$2y$10$3jJMY6P/QzliiyRXUGDhVO.IRkIrWjPG80HT8P9nkZPEsfPcMDSba', 'seeker', '6785923265', 'active', 'user_8_1775223620.jpg', '2026-04-03 13:37:15', 1, 0, 0, NULL),
(9, 'isha', 'ishakhunt09@gmail.com', '$2y$10$13etFuIsutr7PedUGROKae2zD.6FPwOTKkIVLnbX5TFQDMjIG97vS', 'seeker', '6743888954', 'active', 'user_9_1775223934.jpg', '2026-04-03 13:43:05', 1, 0, 0, NULL),
(10, 'het', 'bhupt123@gmail.com', '$2y$10$M4j/MQ02sLvLd6FjuPaXj.1pLlrHiSmmD1zLN4IOs/hGrK9cyyIGq', 'seeker', '9677586798', 'active', NULL, '2026-04-03 17:38:15', 1, 0, 0, NULL),
(11, 'spotify', 'kailaskathiriya44@gmail.com', '$2y$10$k4Coockl01IfpX68giYYs.GGk64Otf8jJagwJkiUZchOnH9W2XM7y', 'company', '6789054323', 'active', 'company_11_1775290015.jpg', '2026-04-04 08:04:00', 1, 0, 0, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`aid`),
  ADD KEY `uid` (`uid`);

--
-- Indexes for table `application`
--
ALTER TABLE `application`
  ADD PRIMARY KEY (`aid`),
  ADD UNIQUE KEY `unique_user_job` (`uid`,`jid`),
  ADD KEY `jid` (`jid`),
  ADD KEY `sid` (`sid`);

--
-- Indexes for table `company`
--
ALTER TABLE `company`
  ADD PRIMARY KEY (`cid`),
  ADD KEY `uid` (`uid`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`fid`),
  ADD KEY `uid` (`uid`);

--
-- Indexes for table `fraud_reports`
--
ALTER TABLE `fraud_reports`
  ADD PRIMARY KEY (`fr_id`);

--
-- Indexes for table `job`
--
ALTER TABLE `job`
  ADD PRIMARY KEY (`jid`),
  ADD KEY `cid` (`cid`),
  ADD KEY `fk_jobs_company` (`uid`);

--
-- Indexes for table `job_offers`
--
ALTER TABLE `job_offers`
  ADD PRIMARY KEY (`oid`),
  ADD KEY `fk_offer_company` (`cid`),
  ADD KEY `fk_offer_seeker` (`sid`),
  ADD KEY `fk_offer_job` (`jid`);

--
-- Indexes for table `job_seeker`
--
ALTER TABLE `job_seeker`
  ADD PRIMARY KEY (`sid`),
  ADD KEY `uid` (`uid`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`nid`),
  ADD KEY `uid` (`uid`);

--
-- Indexes for table `saved_candidate`
--
ALTER TABLE `saved_candidate`
  ADD PRIMARY KEY (`scid`),
  ADD KEY `fk_company` (`cid`),
  ADD KEY `fk_seeker` (`sid`);

--
-- Indexes for table `saved_job`
--
ALTER TABLE `saved_job`
  ADD KEY `jid` (`jid`),
  ADD KEY `fk_jobseeker_user` (`uid`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`uid`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `aid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `application`
--
ALTER TABLE `application`
  MODIFY `aid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `company`
--
ALTER TABLE `company`
  MODIFY `cid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `fid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `fraud_reports`
--
ALTER TABLE `fraud_reports`
  MODIFY `fr_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `job`
--
ALTER TABLE `job`
  MODIFY `jid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `job_offers`
--
ALTER TABLE `job_offers`
  MODIFY `oid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `job_seeker`
--
ALTER TABLE `job_seeker`
  MODIFY `sid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `nid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=102;

--
-- AUTO_INCREMENT for table `saved_candidate`
--
ALTER TABLE `saved_candidate`
  MODIFY `scid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `uid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin`
--
ALTER TABLE `admin`
  ADD CONSTRAINT `admin_ibfk_1` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`) ON DELETE CASCADE;

--
-- Constraints for table `application`
--
ALTER TABLE `application`
  ADD CONSTRAINT `application_ibfk_1` FOREIGN KEY (`jid`) REFERENCES `job` (`jid`) ON DELETE CASCADE,
  ADD CONSTRAINT `application_ibfk_2` FOREIGN KEY (`sid`) REFERENCES `job_seeker` (`sid`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_app_company` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`) ON DELETE CASCADE;

--
-- Constraints for table `company`
--
ALTER TABLE `company`
  ADD CONSTRAINT `company_ibfk_1` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`) ON DELETE CASCADE;

--
-- Constraints for table `feedback`
--
ALTER TABLE `feedback`
  ADD CONSTRAINT `feedback_ibfk_1` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`);

--
-- Constraints for table `job`
--
ALTER TABLE `job`
  ADD CONSTRAINT `fk_jobs_company` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`) ON DELETE CASCADE,
  ADD CONSTRAINT `job_ibfk_1` FOREIGN KEY (`cid`) REFERENCES `company` (`cid`) ON DELETE CASCADE;

--
-- Constraints for table `job_offers`
--
ALTER TABLE `job_offers`
  ADD CONSTRAINT `fk_offer_company` FOREIGN KEY (`cid`) REFERENCES `company` (`cid`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_offer_job` FOREIGN KEY (`jid`) REFERENCES `job` (`jid`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_offer_seeker` FOREIGN KEY (`sid`) REFERENCES `job_seeker` (`sid`) ON DELETE CASCADE;

--
-- Constraints for table `job_seeker`
--
ALTER TABLE `job_seeker`
  ADD CONSTRAINT `job_seeker_ibfk_1` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`);

--
-- Constraints for table `saved_candidate`
--
ALTER TABLE `saved_candidate`
  ADD CONSTRAINT `fk_company` FOREIGN KEY (`cid`) REFERENCES `company` (`cid`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_seeker` FOREIGN KEY (`sid`) REFERENCES `job_seeker` (`sid`) ON DELETE CASCADE;

--
-- Constraints for table `saved_job`
--
ALTER TABLE `saved_job`
  ADD CONSTRAINT `fk_jobseeker_user` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `saved_job_ibfk_2` FOREIGN KEY (`jid`) REFERENCES `job` (`jid`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
