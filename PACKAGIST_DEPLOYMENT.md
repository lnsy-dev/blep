# Packagist Deployment Guide

## Pre-deployment Checklist

✅ **composer.json configured**
- Package name: `lnsy-dev/blep`
- Author: LNSY
- License: MIT
- Homepage and support links added

✅ **Git repository**
- Remote: `git@github.com:lnsy-dev/blep.git`
- Make sure all changes are committed and pushed

✅ **Version tagging**
- Create a git tag for your first release

## Deployment Steps

### 1. Tag Your First Release

```bash
git tag -a v1.0.0 -m "Initial release"
git push origin v1.0.0
```

### 2. Submit to Packagist

1. Go to https://packagist.org/
2. Sign in with your GitHub account (lnsy-dev)
3. Click "Submit" in the top navigation
4. Enter your repository URL: `https://github.com/lnsy-dev/blep`
5. Click "Check"
6. If validation passes, click "Submit"

### 3. Set Up Auto-Update Hook (Recommended)

After submission, Packagist will show you a webhook URL. Add it to your GitHub repository:

1. Go to https://github.com/lnsy-dev/blep/settings/hooks
2. Click "Add webhook"
3. Paste the Packagist webhook URL
4. Content type: `application/json`
5. Select "Just the push event"
6. Click "Add webhook"

This ensures Packagist automatically updates when you push new tags.

## Usage After Deployment

Users can install your package with:

```bash
composer require lnsy-dev/blep
```

Or globally:

```bash
composer global require lnsy-dev/blep
```

## Future Releases

For each new release:

```bash
# Update CHANGELOG.md with changes
# Commit all changes
git add .
git commit -m "Release v1.1.0"

# Tag the release
git tag -a v1.1.0 -m "Release v1.1.0"

# Push everything
git push origin main
git push origin v1.1.0
```

Packagist will auto-update via the webhook.

## Semantic Versioning

Follow semver (https://semver.org/):
- **MAJOR** (v2.0.0): Breaking changes
- **MINOR** (v1.1.0): New features, backward compatible
- **PATCH** (v1.0.1): Bug fixes, backward compatible

## Package Badge

Add to your README.md:

```markdown
[![Packagist Version](https://img.shields.io/packagist/v/lnsy-dev/blep)](https://packagist.org/packages/lnsy-dev/blep)
[![Packagist Downloads](https://img.shields.io/packagist/dt/lnsy-dev/blep)](https://packagist.org/packages/lnsy-dev/blep)
```
