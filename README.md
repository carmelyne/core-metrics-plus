# Core Metrics Plus

A WordPress plugin designed to optimize Core Web Vitals through smart resource loading and performance enhancements.

![WordPress Version](https://img.shields.io/wordpress/plugin/wp-version/core-metrics-plus)
![PHP Version](https://img.shields.io/badge/PHP-7.4%2B-blue)
![License](https://img.shields.io/github/license/carmelyne/core-metrics-plus)

## 📁 Repository Structure

```
core-metrics-plus/
├── assets/                    # Distribution files
│   └── *.zip                 # Release packages
├── docs/                     # Documentation
│   ├── AI_ASSIST.md         # AI Development workflow
│   ├── CHANGELOG.md         # Version history
│   ├── LICENSE.md           # GPL v2 license
│   ├── PERFORMANCE.md       # Performance benchmarks
│   ├── TESTING.md          # Testing guidelines
│   └── USER_STORIES.md      # Feature specifications
├── includes/                 # Core plugin files
│   ├── critical-css.php     # CSS optimization
│   └── script-optimizer.php # Script management
├── plugin-update-checker/    # Update system
├── vendor/                   # Dependencies
├── composer.json            # Package management
├── sudolang.yaml            # AI development config
└── README.md               # This file
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

- [AI Development](docs/AI_ASSIST.md) - Development workflow and AI guidelines
- [Changelog](docs/CHANGELOG.md) - Version history and updates
- [Performance](docs/PERFORMANCE.md) - Performance benchmarks and targets
- [Testing Guidelines](docs/TESTING.md) - Testing procedures and requirements
- [User Stories](docs/USER_STORIES.md) - Feature specifications and requirements

## 🤝 Contributing

We follow an AI-assisted, user-story-driven development approach. Please follow these steps:

1. Review our [development workflow](docs/AI_ASSIST.md)
2. Check the [AI development configuration](sudolang.yaml) for guidelines
3. Start with a user story in [USER_STORIES.md](docs/USER_STORIES.md)
4. Fork the repository
5. Create a feature branch following our naming convention
6. Make your changes
7. Submit a pull request with appropriate documentation

## 💬 Support

- Create an [issue](https://github.com/carmelyne/core-metrics-plus/issues) for bug reports
- Start a [discussion](https://github.com/carmelyne/core-metrics-plus/discussions) for questions
- Check [performance docs](docs/PERFORMANCE.md) for optimization tips
- Review [user stories](docs/USER_STORIES.md) for feature requests

## 📄 License

This project is licensed under the GPL v2 or later - see the [LICENSE.md](docs/LICENSE.md) file for details.

## 👏 Credits

Developed by [Carmelyne](https://github.com/carmelyne)

Special thanks to:
- [Plugin Update Checker](https://github.com/YahnisElsts/plugin-update-checker) by Yahnis Elsts
- WordPress Core Web Vitals team for performance metrics guidance
