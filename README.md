# Core Metrics Plus

A WordPress plugin designed to optimize Core Web Vitals through smart resource loading and performance enhancements.

![WordPress Version](https://img.shields.io/wordpress/plugin/wp-version/core-metrics-plus)
![PHP Version](https://img.shields.io/badge/PHP-7.4%2B-blue)
![License](https://img.shields.io/github/license/carmelyne/core-metrics-plus)

## 📁 Repository Structure

```
core-metrics-plus/
├── assets/               # Distribution files
│   └── *.zip            # Release packages
├── docs/                # Documentation
│   ├── AI_ASSIST.md     # Development workflow
│   ├── CHANGELOG.md     # Version history
│   ├── LICENSE.md       # GPL v2 license
│   ├── USER_STORIES.md  # Feature specifications
│   └── TESTING.md       # Testing guidelines
├── src/                 # Source code
│   └── *.php           # PHP source files
├── composer.json        # Dependencies
├── sudolang.yaml        # AI configuration
└── README.md           # This file
```

## ✨ Features

- 🚀 **Fetch Priority Optimization**
  - Automatically sets optimal fetch priority for images and videos
  - Improves Largest Contentful Paint (LCP) scores
  - Smart resource loading based on viewport visibility

- 📊 **Performance Monitoring**
  - Real-time performance logging
  - Detailed console timing measurements
  - Resource optimization tracking

- 🛠 **Script Management**
  - Intelligent script defer implementation
  - jQuery optimization
  - Non-blocking resource loading

- 🔍 **Debug Tools**
  - Comprehensive performance logging
  - Detailed console messages
  - Resource timing analysis

## 🤖 AI-First Development

Core Metrics Plus follows an AI-first development approach, making it easy for both human developers and AI assistants to contribute. The following files work together to enable seamless AI collaboration:

### `sudolang.yaml`
Our primary AI configuration file that defines:
- Project metadata and goals
- Development workflows and standards
- Code quality requirements
- Testing procedures
- Release processes

Example usage:
```yaml
# AI can understand our workflow
development_workflow:
  feature_development:
    steps:
      1_user_story: 
        description: "Create user story"
        location: "docs/USER_STORIES.md"
```

### `docs/AI_ASSIST.md`
Provides detailed guidelines for AI assistants:
- Code generation rules
- Documentation requirements
- Testing expectations
- Performance thresholds
- Security standards

### `docs/USER_STORIES.md`
Contains feature specifications in Gherkin format:
```gherkin
Feature: Script Optimization
  As a website owner
  I want scripts to load efficiently
  So that my pages load faster

  Scenario: Defer Non-Critical Scripts
    Given a non-critical script
    When the page loads
    Then the script should be deferred
```

### AI Development Flow
1. AI reads `sudolang.yaml` for project context
2. Follows guidelines in `AI_ASSIST.md`
3. Implements features based on `USER_STORIES.md`
4. Creates PRs with required documentation
5. Ensures all standards are met

This structured approach ensures consistent development practices whether you're working with GitHub Copilot, ChatGPT, or other AI assistants.

## 📥 Installation

1. Download the latest release from the [releases page](https://github.com/carmelyne/core-metrics-plus/releases)
2. Upload the plugin through WordPress admin or extract to your `wp-content/plugins/` directory
3. Activate the plugin through WordPress admin interface
4. No additional configuration needed - works out of the box!

## ⚙️ Requirements

- WordPress 5.0 or higher
- PHP 7.4 or higher
- Modern browser with console support (for debugging features)

## 🔄 Updates

Core Metrics Plus uses GitHub releases for updates. When a new version is available:
1. You'll see an update notice in your WordPress admin
2. Review the changelog
3. Update with one click through the WordPress plugin updater

## 📚 Documentation

- [Changelog](docs/CHANGELOG.md) - Version history and updates
- [Testing Guidelines](docs/TESTING.md) - Testing procedures and requirements

## 🤝 Contributing

We welcome contributions! Please follow these steps:

1. Check our [development guidelines](docs/AI_ASSIST.md) for development workflow
2. Fork the repository
3. Create a feature branch
4. Make your changes
5. Submit a pull request

## 💬 Support

- Create an [issue](https://github.com/carmelyne/core-metrics-plus/issues) for bug reports
- Start a [discussion](https://github.com/carmelyne/core-metrics-plus/discussions) for questions
- Check [troubleshooting](docs/AI_ASSIST.md#troubleshooting) for common issues

## 📄 License

This project is licensed under the GPL v2 or later - see the [LICENSE.md](docs/LICENSE.md) file for details.

## 👏 Credits

Developed by [Carmelyne](https://github.com/carmelyne)

Special thanks to:
- [Plugin Update Checker](https://github.com/YahnisElsts/plugin-update-checker) by Yahnis Elsts
- WordPress Core Web Vitals team for performance metrics guidance
