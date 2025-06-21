# Contributing to Laravel Visual Builder

Thank you for considering contributing to Laravel Visual Builder! This document outlines the process for contributing to this project.

## Code of Conduct

This project and everyone participating in it is governed by our Code of Conduct. By participating, you are expected to uphold this code.

## How Can I Contribute?

### Reporting Bugs

- Check if the bug has already been reported in the issues section
- Use the bug report template when creating a new issue
- Include detailed steps to reproduce the bug
- Include screenshots if applicable
- Specify your environment (PHP version, Laravel version, etc.)

### Suggesting Enhancements

- Check if the enhancement has already been suggested
- Use the feature request template
- Provide a clear description of the enhancement
- Explain why this enhancement would be useful
- Include any relevant examples

### Pull Requests

1. Fork the repository
2. Create a new branch for your feature
3. Make your changes
4. Write or update tests as needed
5. Ensure all tests pass
6. Submit a pull request

### Development Setup

1. Clone the repository:
```bash
git clone https://github.com/laravel-builder/visual-builder.git
```

2. Install dependencies:
```bash
composer install
```

3. Copy the environment file:
```bash
cp .env.example .env
```

4. Generate application key:
```bash
php artisan key:generate
```

5. Run tests:
```bash
composer test
```

### Coding Standards

- Follow PSR-12 coding standards
- Use type hints and return types
- Write meaningful commit messages
- Document your code with PHPDoc blocks
- Write unit tests for new features

### Testing

- Write tests for all new features
- Ensure all tests pass before submitting PR
- Maintain or improve code coverage

### Documentation

- Update README.md if needed
- Add PHPDoc blocks to new classes and methods
- Update configuration documentation
- Add examples for new features

## Pull Request Process

1. Update the README.md with details of changes if needed
2. Update the documentation with any new configuration options
3. The PR will be merged once you have the sign-off of at least one maintainer

## Development Workflow

1. Create a new branch for your feature
2. Make your changes
3. Write tests
4. Update documentation
5. Submit PR
6. Address any feedback
7. Get merged!

## Questions?

Feel free to open an issue for any questions you might have.

Thank you for contributing to Laravel Visual Builder! 