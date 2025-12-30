# Basic Examples

This folder contains the most fundamental examples to get started with WebFiori HTTP library.

## Examples

1. **[01-hello-world](01-hello-world/)** - Minimal service implementation
2. **[02-with-parameters](02-with-parameters/)** - Basic parameter handling
3. **[03-multiple-methods](03-multiple-methods/)** - Supporting different HTTP methods
4. **[04-simple-manager](04-simple-manager/)** - Basic service manager setup

## Prerequisites

- PHP 8.1+
- WebFiori HTTP library installed via Composer

## Running Examples

Each example can be run independently:

```bash
cd 01-hello-world
php -S localhost:8080
```

Then test with:
```bash
curl "http://localhost:8080?service=hello"
```
