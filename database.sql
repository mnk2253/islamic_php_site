
CREATE TABLE `admin` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(100),
  `password` VARCHAR(255)
);

CREATE TABLE `quran` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `surah` VARCHAR(100),
  `ayat` TEXT,
  `meaning` TEXT
);

CREATE TABLE `hadith` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `narrator` VARCHAR(100),
  `hadith_text` TEXT,
  `source` VARCHAR(100)
);

CREATE TABLE `videos` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(255),
  `youtube_link` TEXT,
  `video_file` VARCHAR(255) DEFAULT NULL
);

CREATE TABLE `messages` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100),
  `email` VARCHAR(100),
  `message` TEXT
);
