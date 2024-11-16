# Core Metrics Plus Performance Documentation

## Performance Optimization Strategy

### Critical CSS Optimization
Our approach to Critical CSS is intentionally simple yet effective:

1. **Admin Style Exclusion**
   - Instead of complex critical path analysis, we focus on excluding admin-specific styles
   - Admin styles follow consistent naming patterns in WordPress
   - This approach is reliable across different themes and plugins

2. **Why This Works**
   - WordPress admin styles are predictable (`wp-admin`, `admin-bar`, etc.)
   - Frontend styles are preserved intact
   - No risk of breaking theme functionality
   - Minimal performance overhead

### Script Optimization
We take a conservative approach to script optimization:

1. **Analytics/Tracking Scripts**
   - Safely loaded with `async` attribute
   - Identified through consistent naming patterns
   - No impact on core functionality

2. **Core Scripts Protection**
   - WordPress core scripts are left untouched
   - jQuery and dependencies preserved
   - No aggressive reordering or defer

## Best Practices
- Always test performance changes across different themes
- Monitor Core Web Vitals before and after changes
- Use real-world testing with various WordPress configurations
- Keep optimization logic simple and maintainable
