# E-Commerce Example Project

This directory contains a realistic example of using the Business Logic Documentation Generator on a fictional e-commerce application.

## Structure

```
example/
├── src/                    # Example PHP source files with @bl-* annotations
│   ├── OrderService.php
│   ├── DiscountEngine.php
│   ├── ShippingCalculator.php
│   ├── ReturnProcessor.php
│   ├── PaymentGateway.php
│   └── CustomerAccount.php
├── output/                 # Pre-generated documentation (for preview)
├── generate.sh             # Script to regenerate documentation
└── README.md              # This file
```

## Topics Covered

The example demonstrates business logic documentation for:

- **Order Processing** — Order validation, status workflow, fraud detection
- **Discount Rules** — Coupon codes, automatic discounts, loyalty program
- **Shipping** — Shipping methods, address validation, delivery restrictions
- **Returns Policy** — Return windows, return shipping, refund processing
- **Payment Processing** — Payment methods, security, refunds and chargebacks
- **Customer Accounts** — Account creation, verification, loyalty tiers

## Usage

### Generate documentation

```bash
./generate.sh
```

This will:
1. Run the documentation generator on the `src/` directory
2. Output HTML files to `output/`
3. Attempt to open the result in your browser

### Manual generation

```bash
php ../bl-doc-gen.php -o output/ -t "E-Commerce Business Rules" src/
```

## Preview

A pre-generated version of the documentation is included in `output/` so you can see the result without running the tool.

Open `output/index.html` in your browser to explore the documentation.

## Key Features Demonstrated

- Multiple topics across multiple files
- Cross-references between related topics (e.g., Order Processing → Payment Processing)
- Mix of docblock and inline comment annotations
- Realistic business rules and constraints
- Hierarchical organization with topics and subtopics
