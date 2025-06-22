# FRESH Color Standard for Nucleo-Map Project

## 🎨 **Official Color Palette - Based on Tailwind CSS**

### **🔮 PRIMARY (Purple) - Main Brand Color**
```css
/* Primary Purple (#A05AFF) */
primary-50:  #f3f1ff   /* Very light purple background */
primary-100: #e8e2ff   /* Light purple background */
primary-200: #d1c4ff   /* Soft purple background */
primary-300: #b8a3ff   /* Medium light purple */
primary-400: #9f82ff   /* Medium purple */
primary-500: #a05aff   /* BASE PRIMARY COLOR */
primary-600: #8b47e6   /* Dark purple (preferred for buttons) */
primary-700: #7636cc   /* Darker purple (hover states) */
primary-800: #612bb3   /* Very dark purple */
primary-900: #4c2199   /* Darkest purple */
```

### **💚 ACCENT (Green) - Success & Medical Centers**
```css
/* Accent Green (#1BCFB4) */
accent-50:  #f0fdfa    /* Very light green background */
accent-100: #ccfbf1    /* Light green background */
accent-200: #99f6e4    /* Soft green background */
accent-300: #5eead4    /* Medium light green */
accent-400: #2dd4bf    /* Medium green */
accent-500: #1bcfb4    /* BASE ACCENT COLOR */
accent-600: #0d9488    /* Dark green (preferred for buttons) */
accent-700: #0f766e    /* Darker green (hover states) */
accent-800: #115e59    /* Very dark green */
accent-900: #134e4a    /* Darkest green */
```

### **🔵 SECONDARY (Blue) - Secondary Actions**
```css
/* Secondary Blue (#4BCBEB) */
secondary-50:  #f0f9ff  /* Very light blue background */
secondary-100: #e0f2fe  /* Light blue background */
secondary-200: #bae6fd  /* Soft blue background */
secondary-300: #7dd3fc  /* Medium light blue */
secondary-400: #38bdf8  /* Medium blue */
secondary-500: #4bcbeb  /* BASE SECONDARY COLOR */
secondary-600: #0ea5e9  /* Dark blue (preferred for buttons) */
secondary-700: #0284c7  /* Darker blue (hover states) */
secondary-800: #0369a1  /* Very dark blue */
secondary-900: #0c4a6e  /* Darkest blue */
```

### **⚠️ WARNING (Pink) - Warning States**
```css
/* Warning Pink (#FE9496) */
warning-50:  #fef2f2   /* Very light pink background */
warning-100: #fee2e2   /* Light pink background */
warning-200: #fecaca   /* Soft pink background */
warning-300: #fca5a5   /* Medium light pink */
warning-400: #f87171   /* Medium pink */
warning-500: #fe9496   /* BASE WARNING COLOR */
warning-600: #ef4444   /* Dark pink (preferred for buttons) */
warning-700: #dc2626   /* Darker pink (hover states) */
warning-800: #b91c1c   /* Very dark pink */
warning-900: #991b1b   /* Darkest pink */
```

### **🔥 DANGER (Deep Purple) - Error & Delete Actions**
```css
/* Danger Purple (#9E58FF) */
danger-50:  #faf5ff    /* Very light purple background */
danger-100: #f3e8ff    /* Light purple background */
danger-200: #e9d5ff    /* Soft purple background */
danger-300: #d8b4fe    /* Medium light purple */
danger-400: #c084fc    /* Medium purple */
danger-500: #9e58ff    /* BASE DANGER COLOR */
danger-600: #a855f7    /* Dark purple (preferred for buttons) */
danger-700: #9333ea    /* Darker purple (hover states) */
danger-800: #7c3aed    /* Very dark purple */
danger-900: #6b21a8    /* Darkest purple */
```

---

## 🔧 **Tailwind CSS Configuration**

### **Add to `tailwind.config.js`:**

```javascript
module.exports = {
  theme: {
    extend: {
      colors: {
        // Primary brand color (Purple)
        primary: {
          50: '#f3f1ff',
          100: '#e8e2ff',
          200: '#d1c4ff',
          300: '#b8a3ff',
          400: '#9f82ff',
          500: '#a05aff', // Base
          600: '#8b47e6', // Preferred button
          700: '#7636cc', // Hover
          800: '#612bb3',
          900: '#4c2199',
        },
        // Accent color (Green)
        accent: {
          50: '#f0fdfa',
          100: '#ccfbf1',
          200: '#99f6e4',
          300: '#5eead4',
          400: '#2dd4bf',
          500: '#1bcfb4', // Base
          600: '#0d9488', // Preferred button
          700: '#0f766e', // Hover
          800: '#115e59',
          900: '#134e4a',
        },
        // Secondary color (Blue)
        secondary: {
          50: '#f0f9ff',
          100: '#e0f2fe',
          200: '#bae6fd',
          300: '#7dd3fc',
          400: '#38bdf8',
          500: '#4bcbeb', // Base
          600: '#0ea5e9', // Preferred button
          700: '#0284c7', // Hover
          800: '#0369a1',
          900: '#0c4a6e',
        },
        // Warning color (Pink)
        warning: {
          50: '#fef2f2',
          100: '#fee2e2',
          200: '#fecaca',
          300: '#fca5a5',
          400: '#f87171',
          500: '#fe9496', // Base
          600: '#ef4444', // Preferred button
          700: '#dc2626', // Hover
          800: '#b91c1c',
          900: '#991b1b',
        },
        // Danger color (Deep Purple)
        danger: {
          50: '#faf5ff',
          100: '#f3e8ff',
          200: '#e9d5ff',
          300: '#d8b4fe',
          400: '#c084fc',
          500: '#9e58ff', // Base
          600: '#a855f7', // Preferred button
          700: '#9333ea', // Hover
          800: '#7c3aed',
          900: '#6b21a8',
        },
      },
    },
  },
}
```

---

## 📐 **Usage Guidelines**

### **🎯 When to Use Each Color:**

| Color | Use Case | Examples |
|-------|----------|----------|
| **Primary** | Main actions, brand elements | Login buttons, primary CTAs, active navigation |
| **Accent** | Success states, medical centers | Success messages, center-related features, checkmarks |
| **Secondary** | Secondary actions, information | Back buttons, info panels, secondary CTAs |
| **Warning** | Caution, non-critical alerts | Form validation warnings, caution messages |
| **Danger** | Errors, destructive actions | Delete buttons, error messages, critical alerts |

### **🔘 Recommended Shades:**

- **Background**: Use 50-100 for light backgrounds
- **Text**: Use 600-800 for readable text
- **Buttons**: Use 600 as base, 700 for hover
- **Borders**: Use 200-300 for subtle borders
- **Icons**: Use 500-600 for icons

---

## ✅ **Best Practices**

### **DO's:**
- ✅ Use semantic color names (primary, accent, secondary)
- ✅ Use consistent shades across similar components
- ✅ Test color contrast for accessibility
- ✅ Use 600 shade for buttons, 700 for hover states
- ✅ Use 50-100 shades for light backgrounds

### **DON'Ts:**
- ❌ Don't use arbitrary color values
- ❌ Don't mix different color systems
- ❌ Don't use too many colors in one interface
- ❌ Don't use colors without sufficient contrast
- ❌ Don't use default Tailwind colors (blue-500, etc.)

---

## 🚀 **Quick Reference**

### **Most Common Combinations:**

```css
/* Button Styles */
.btn-primary   { @apply bg-primary-600 hover:bg-primary-700 text-white; }
.btn-accent    { @apply bg-accent-600 hover:bg-accent-700 text-white; }
.btn-secondary { @apply bg-secondary-600 hover:bg-secondary-700 text-white; }
.btn-danger    { @apply bg-danger-600 hover:bg-danger-700 text-white; }

/* Status Badges */
.badge-success { @apply bg-accent-100 text-accent-800; }
.badge-warning { @apply bg-warning-100 text-warning-800; }
.badge-error   { @apply bg-danger-100 text-danger-800; }
.badge-info    { @apply bg-secondary-100 text-secondary-800; }

/* Form Elements */
.input-focus   { @apply focus:border-primary-500 focus:ring-primary-500; }
.input-error   { @apply border-danger-300 focus:border-danger-500 focus:ring-danger-500; }
```

---

## 🎨 **Visual Color Palette**

For visual reference, see: `resources/views/color-palette.blade.php`

---

**🚨 IMPORTANT**: This color standard MUST be followed for ALL Blade templates and CSS in the Nucleo-Map project.

---

*Created: {{ date('Y-m-d') }}*
*Version: 1.0 - Official Nucleo-Map Color Standard* 