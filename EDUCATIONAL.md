# 📚 Educational Purpose & Legal Notice

## ⚠️ IMPORTANT DISCLAIMER

**ZentrixStream is an educational project created for the sole purpose of demonstrating modern web development techniques.**

### What This Project Demonstrates

This codebase serves as a learning resource for:

1. **Full-Stack Web Development**
   - Server-side PHP programming
   - Client-side JavaScript interactivity
   - Database design and management
   - API integration patterns

2. **Modern Frontend Techniques**
   - Responsive CSS with Tailwind CSS
   - Dynamic UI components
   - Carousel and modal implementations
   - Mobile-first design principles

3. **Backend Architecture**
   - RESTful and GraphQL API consumption
   - User authentication and session management
   - Database schema design
   - Security best practices (prepared statements, XSS prevention)

4. **Third-Party API Integration**
   - AniList GraphQL API for anime metadata
   - TMDB REST API for movie/TV metadata
   - Video embedding services

### What This Project Does NOT Do

❌ **Host any copyrighted content** - All media is embedded from third-party services  
❌ **Distribute pirated materials** - No video files are stored on our servers  
❌ **Encourage illegal streaming** - Users must comply with local laws  
❌ **Monetize copyrighted works** - No ads or revenue from protected content  

---

## 🔒 Legal Compliance

### Copyright Notice

All anime, movies, and TV shows displayed through this interface are the property of their respective copyright holders:
- Anime content © respective studios and licensors
- Movie content © respective production companies and distributors
- TV content © respective networks and production companies

### User Responsibility

By using this software, you acknowledge that:

1. **You are responsible for complying with local copyright laws**
2. **Streaming copyrighted content without authorization may be illegal in your jurisdiction**
3. **The developers assume no liability for user actions**
4. **This tool is provided "as is" without warranties of any kind**

### DMCA & Takedown

If you are a copyright holder and believe this software facilitates infringement:

1. This project contains NO actual media files
2. We do not host, upload, or distribute content
3. All content is retrieved via public APIs and third-party embeds
4. Contact the respective embedding services for content removal

---

## 🎯 Intended Use Cases

### Appropriate Uses

✅ **Learning PHP/MySQL development**  
✅ **Studying API integration patterns**  
✅ **Building portfolio projects**  
✅ **Creating legal streaming interfaces for owned content**  
✅ **Developing skills for legitimate streaming platform jobs**  

### Inappropriate Uses

❌ **Public deployment for piracy**  
❌ **Commercial exploitation of copyrighted content**  
❌ **Circumventing content protection**  
❌ **Redistribution as a "free streaming site"**  

---

## 🛠️ For Developers

### Learning Checklist

When studying this codebase, focus on:

- [ ] How GraphQL queries are structured and executed
- [ ] Database connection pooling and prepared statements
- [ ] Session management and authentication flow
- [ ] Responsive grid layouts with CSS
- [ ] API error handling and fallbacks
- [ ] Input validation and sanitization

### Security Lessons

This codebase demonstrates:
- SQL injection prevention via prepared statements
- XSS protection through output encoding
- Password hashing with bcrypt
- CSRF token implementation
- Secure session configuration

---

## 📖 Database Schema Reference

### Required Tables

```sql
-- Users Table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Watch History Table
CREATE TABLE watch_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    anime_id VARCHAR(50) NOT NULL,
    anime_title VARCHAR(255) NOT NULL,
    episode INT DEFAULT 1,
    watched_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_watch (user_id, anime_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Page Analytics Table
CREATE TABLE pageview (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pageID VARCHAR(100) UNIQUE NOT NULL,
    totalview INT DEFAULT 0,
    like_count INT DEFAULT 0,
    dislike_count INT DEFAULT 0,
    animeID VARCHAR(50)
);
```

---

## 🔧 API Configuration Guide

### AniList API (Free - No Key Required)

The AniList GraphQL API requires no authentication for public queries:

```php
$endpoint = 'https://graphql.anilist.co';
$headers = [
    'Content-Type: application/json',
    'Accept: application/json'
];
```

Rate Limits:
- 90 requests per minute (unauthenticated)
- Respect the API - implement caching where possible

### TMDB API (Free API Key Required)

1. Register at [themoviedb.org](https://www.themoviedb.org)
2. Request API key in account settings
3. Add to `movie/.env`:
   ```
   TMDB_API_KEY=your_api_key_here
   ```

Rate Limits:
- 40 requests per 10 seconds (standard tier)
- Check response headers for rate limit status

---

## 🌍 Open Source Philosophy

This project is released under GPL-3.0 to promote:

- **Education** - Learning from real-world code examples
- **Transparency** - Understanding how streaming interfaces work
- **Innovation** - Building better legal streaming solutions
- **Community** - Sharing knowledge with fellow developers

### Contributing

When contributing:
1. Maintain educational focus
2. Document security improvements
3. Respect third-party API terms of service
4. Do not add features that facilitate piracy

---

## ❓ FAQ

**Q: Can I deploy this publicly?**  
A: The code itself can be deployed, but streaming copyrighted content without license may violate laws in your jurisdiction. Consult legal counsel.

**Q: Does this host anime/movies?**  
A: No. All content is embedded from third-party services. This is purely an interface layer.

**Q: Is this legal to use?**  
A: The code itself is legal (GPL-3.0). Usage depends on your local laws and what content you access.

**Q: Can I use this for my own content?**  
A: Absolutely! This architecture works great for legal streaming platforms.

**Q: Why GPL-3.0 license?**  
A: To ensure any derivatives remain open source and educational.

---

## 📧 Contact & Issues

For educational questions about the codebase:
- Open a GitHub Issue
- Submit a Pull Request
- Start a Discussion

**Note:** We do not provide support for deploying public streaming sites or circumventing content protections.

---

<p align="center">
  <strong>Remember: With great code comes great responsibility.</strong>
</p>

<p align="center">
  Use this knowledge to build amazing, legal, and ethical applications.
</p>
