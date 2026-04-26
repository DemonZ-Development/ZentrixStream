# Contributing to ZentrixStream

Thank you for your interest in contributing to ZentrixStream! This document provides guidelines and instructions for contributing.

## 🤝 Code of Conduct

This project and everyone participating in it is governed by our commitment to:
- **Educational value** - All contributions should enhance learning
- **Security awareness** - Help developers understand secure coding
- **Legal compliance** - Respect copyright and applicable laws

## 🚀 How to Contribute

### Reporting Bugs

Before creating a bug report, please:
1. Check if the issue already exists
2. Use the bug report template
3. Include steps to reproduce
4. Specify your environment (PHP version, OS, etc.)

**Security bugs:** Do NOT open public issues. Email maintainers directly.

### Suggesting Enhancements

Enhancement suggestions should:
1. Focus on educational value
2. Improve code quality or security
3. Not facilitate piracy or copyright infringement
4. Use the feature request template

### Pull Requests

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Make your changes
4. Test thoroughly
5. Update documentation if needed
6. Commit with clear messages
7. Push to your fork
8. Open a Pull Request

## 📝 Coding Standards

### PHP Guidelines

- Use **prepared statements** for all database queries
- **Never** concatenate user input into SQL
- Escape output with `htmlspecialchars()`
- Use `password_hash()` and `password_verify()` for passwords
- Follow PSR-12 coding standards where applicable

Example of secure code:
```php
// GOOD: Prepared statement
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();

// GOOD: Output escaping
echo htmlspecialchars($userInput, ENT_QUOTES, 'UTF-8');

// BAD: Never do this
$query = "SELECT * FROM users WHERE id = $userId"; // SQL injection!
echo $userInput; // XSS vulnerability!
```

### JavaScript Guidelines

- Use modern ES6+ features
- Avoid inline event handlers
- Validate inputs client-side AND server-side
- Handle API errors gracefully

### CSS Guidelines

- Use Tailwind CSS utility classes
- Maintain dark theme consistency
- Ensure mobile responsiveness
- Test on multiple browsers

## 🛡️ Security Requirements

All contributions must:

- [ ] Use prepared statements for database queries
- [ ] Escape all user-generated output
- [ ] Validate and sanitize inputs
- [ ] Include CSRF tokens for state-changing operations
- [ ] Not expose API keys in client-side code
- [ ] Use HTTPS for all external resources

## 📚 Documentation

Update documentation when:
- Adding new features
- Changing configuration options
- Modifying database schema
- Adding new API integrations

## 🧪 Testing

Before submitting:

1. Test on PHP 8.0+
2. Verify MySQL compatibility
3. Check mobile responsiveness
4. Test with different browsers
5. Ensure no PHP syntax errors

## 🎯 What We Accept

### ✅ We Welcome

- Security improvements
- Bug fixes
- Performance optimizations
- Educational code comments
- UI/UX improvements
- Documentation updates
- New legal API integrations

### ❌ We Reject

- Features that facilitate piracy
- Hardcoded credentials
- Backdoors or malicious code
- Copyright-infringing content
- Public deployment guides for illegal use

## 📜 License

By contributing, you agree that your contributions will be licensed under the GPL-3.0 License.

## 🙏 Recognition

Contributors will be acknowledged in:
- Release notes
- CONTRIBUTORS.md file
- Project documentation

---

**Questions?** Open a discussion or email the maintainers.

Thank you for helping make ZentrixStream a better educational resource!
