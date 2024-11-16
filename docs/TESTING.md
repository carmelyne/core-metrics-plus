# Testing Checklist for Fetch Priority Plus

## Pre-Installation Check
- [ ] Backup your WordPress site
- [ ] Note current page load times for comparison
- [ ] Enable WP_DEBUG in wp-config.php if needed

## Installation
- [ ] Upload plugin via WordPress admin
- [ ] Activate plugin without errors
- [ ] No PHP errors in debug.log

## Basic Functionality Check

### Image Priority Testing
1. [ ] Open homepage in Chrome
2. [ ] Right-click → Inspect → Elements
3. [ ] Check first 3 images have:
   - [ ] `fetchpriority="high"` attribute
   - [ ] Proper loading order
   - [ ] No broken images

### Video Priority Testing
1. [ ] Locate first video on page
2. [ ] Verify it has `fetchpriority="high"`
3. [ ] Check video loads properly

### Resource Loading
1. [ ] Open Chrome DevTools → Network tab
2. [ ] Enable 'Disable cache'
3. [ ] Refresh page
4. [ ] Check for:
   - [ ] Preloaded resources in header
   - [ ] Deferred non-critical scripts
   - [ ] Proper font loading
   - [ ] Third-party scripts loading after delay

## Performance Testing

### First Contentful Paint (FCP)
1. [ ] Run PageSpeed Insights
2. [ ] Note FCP score
3. [ ] Compare with pre-installation score
4. [ ] Check if critical CSS is inlined

### Total Blocking Time (TBT)
1. [ ] Check Network tab waterfall
2. [ ] Verify scripts are properly deferred
3. [ ] No render-blocking resources
4. [ ] Third-party scripts load after delay

### Largest Contentful Paint (LCP)
1. [ ] Identify LCP element (usually hero image)
2. [ ] Verify it has high priority
3. [ ] Check load time improvement

## Browser Testing
- [ ] Chrome
- [ ] Firefox
- [ ] Safari
- [ ] Mobile browsers

## Compatibility Check
- [ ] Theme functionality intact
- [ ] Other plugins working
- [ ] Forms working
- [ ] Media uploads working
- [ ] Admin panel responsive

## Error Checking
1. [ ] Check browser console for errors
2. [ ] Review wp-content/debug.log
3. [ ] Verify no 404 errors for resources
4. [ ] Check PHP error logs

## Performance Metrics (Before/After)
Record these metrics before and after installation:

| Metric | Before | After |
|--------|---------|--------|
| FCP    |         |        |
| TBT    |         |        |
| LCP    |         |        |

## Common Issues to Watch For
- [ ] Layout shifts during page load
- [ ] Images loading out of order
- [ ] JavaScript functionality breaks
- [ ] Forms not submitting
- [ ] Admin panel slowdown
- [ ] Media library issues

## Recovery Plan
If issues occur:
1. [ ] Check debug.log for errors
2. [ ] Deactivate plugin
3. [ ] Clear browser cache
4. [ ] Clear server cache
5. [ ] Restore backup if needed

## Notes
- Document any issues found
- Note performance improvements
- Record any conflicts with other plugins
- Document browser-specific issues

## Final Approval Checklist
- [ ] All tests passed
- [ ] No critical errors
- [ ] Performance improved
- [ ] No user experience issues
- [ ] All functionality preserved
