# AI Assistance Guidelines for Core Metrics Plus

This document outlines the standard workflows and practices for AI-assisted development of the Core Metrics Plus WordPress plugin.

## Project Overview

### Repository Structure
```
core-metrics-plus/
├── core-metrics-plus.php     # Main plugin file
├── CHANGELOG.md             # Version history
├── composer.json           # Dependencies
├── LICENSE                # Project license
├── TESTING.md            # Testing guidelines
└── plugin-update-checker/ # Update system
```

### Key Components
- **Performance Optimization**: Fetch priority for images/videos
- **Script Management**: Defer non-essential scripts
- **Update System**: GitHub-based automatic updates
- **Debug Tools**: Performance logging and diagnostics

## Version Control Workflow

### Git Best Practices

1. Always check status before making changes:
   ```bash
   git status
   ```

2. Add files individually (NEVER use git add .):
   ```bash
   git add core-metrics-plus.php
   git add docs/CHANGELOG.md
   ```

3. Create semantic commit messages:
   ```bash
   git commit -m "type: Brief description

   - Detailed point 1
   - Detailed point 2"
   ```
   Types: feat, fix, docs, style, refactor, test, chore

4. Tag releases:
   ```bash
   git tag -a vX.X.X -m "Version X.X.X - Brief description"
   git push origin main --tags
   ```

### Making Changes
1. Update version numbers in:
   - `core-metrics-plus.php` (Plugin header and CMP_VERSION)
   - `docs/CHANGELOG.md`

2. Create zip file:
   ```bash
   zip -r assets/core-metrics-plus-[VERSION].zip . -x ".*" -x "__MACOSX"
   ```

3. GitHub Release:
   - Create new release using the tag
   - Copy relevant CHANGELOG section
   - Upload zip file as release asset

### Version Numbering (SemVer)
- MAJOR.MINOR.PATCH
- MAJOR: Breaking changes
- MINOR: New features, backward compatible
- PATCH: Bug fixes, backward compatible

### Commit Message Format
```
Version X.X.X: Brief title

- Detailed change 1
- Detailed change 2
```

## Testing Procedures

### Pre-Release Checklist
1. Version numbers match in all files
2. All files included in zip
3. JavaScript console shows no errors
4. Performance logging works
5. Update checker configured correctly

### Update System Testing
1. Install previous version
2. Create new release on GitHub
3. Verify update notice appears
4. Test update process
5. Verify new features work

## Code Standards

### PHP
- WordPress coding standards
- PHP 7.4+ compatibility
- Proper error handling
- Meaningful function prefixes (`cmp_`)

### JavaScript
- Error handling in try-catch blocks
- Performance logging
- Browser compatibility
- Clear console messages

### Documentation
- PHPDoc for functions
- Inline comments for complex logic
- User-friendly console messages
- Clear changelog entries

## Update System Configuration

### GitHub Integration
```php
$myUpdateChecker = YahnisElsts\PluginUpdateChecker\v5\PucFactory::buildUpdateChecker(
    'https://github.com/carmelyne/core-metrics-plus',
    __FILE__,
    'core-metrics-plus'
);

// Configure to use GitHub releases
$myUpdateChecker->getVcsApi()->enableReleaseAssets();
```

### Release Requirements
1. Proper version tag
2. ZIP file as release asset
3. Changelog in release description
4. Proper file permissions in ZIP

## Common Tasks

### Adding New Features
1. Create feature in main plugin file
2. Add performance logging
3. Update version numbers
4. Document in CHANGELOG.md
5. Test functionality
6. Create release

### Bug Fixes
1. Add error handling
2. Update patch version
3. Document in CHANGELOG.md
4. Test fix
5. Create release

### Performance Improvements
1. Add console.time() measurements
2. Log optimization results
3. Update patch version
4. Document improvements
5. Test performance
6. Create release

## Troubleshooting

### Update Notice Not Appearing
1. Verify version numbers match
2. Check GitHub release exists
3. Verify zip file in release assets
4. Check update checker configuration
5. Clear WordPress transients

### Performance Issues
1. Check console logs
2. Verify script loading order
3. Check fetch priority application
4. Monitor resource timing
5. Review browser network tab

## Future Development

### Planned Features
- Configuration options
- Advanced performance metrics
- User customization settings
- Enhanced debugging tools

### Code Improvements
- Modular architecture
- Enhanced error handling
- Expanded logging options
- Performance optimizations
