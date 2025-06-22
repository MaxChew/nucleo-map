# AI Development Rules for Nucleo-Map Project

## 🚨 **MANDATORY RULES FOR ALL AI ASSISTANTS (Cursor, Copilot, etc.)**

### ⚠️ **BEFORE ANY CODE GENERATION - READ THIS FIRST**

---

## 📋 **STEP 1: Color Standards Check**

### **🔍 ALWAYS CHECK FOR COLOR STANDARDS FIRST**

Before writing ANY Blade template or CSS code, you MUST:

1. **Check if `FRESH_COLOR_STANDARD.md` exists**
2. **If NOT found, create it from Tailwind CSS standards**
3. **If found, read it completely and follow ALL color guidelines**
4. **Never use custom colors without checking the standards first**

### **📄 Required Files to Check (in order):**

```
1. FRESH_COLOR_STANDARD.md (Primary color guide)
2. NUCLEO_MAP_COLOR_STANDARDS.md (Project-specific standards)
3. resources/views/color-palette.blade.php (Visual color reference)
4. tailwind.config.js (Tailwind configuration)
```

---

## 🎨 **STEP 2: Color Usage Rules**

### **✅ APPROVED COLOR PATTERNS**

```css
/* PRIMARY COLORS - Use for main actions */
primary-50 to primary-900    /* Purple #A05AFF variations */

/* ACCENT COLORS - Use for success/medical center features */
accent-50 to accent-900      /* Green #1BCFB4 variations */

/* SECONDARY COLORS - Use for secondary actions */
secondary-50 to secondary-900 /* Blue #4BCBEB variations */

/* WARNING COLORS - Use for warnings */
warning-50 to warning-900    /* Pink #FE9496 variations */

/* DANGER COLORS - Use for errors/delete actions */
danger-50 to danger-900      /* Deep Purple #9E58FF variations */
```

### **❌ FORBIDDEN COLOR PATTERNS**

```css
/* NEVER USE THESE - Use semantic colors instead */
blue-500, purple-500, green-500, red-500, pink-500
indigo-500, cyan-500, emerald-500, rose-500

/* NEVER USE ARBITRARY COLORS */
bg-[#ff0000], text-[#00ff00], border-[#0000ff]
```

---

## 🏗️ **STEP 3: Blade File Structure Rules**

### **📝 MANDATORY BLADE TEMPLATE STRUCTURE**

```php
@extends('admin.layout.master', [
    'title' => 'Page Title',
])

@section('content')
    <div class="v-cloak--hidden py-6 flex flex-col gap-6">
        <div class="px-6">
            <div class="flex items-center justify-between">
                <h1>Page Title</h1>
                <a href="{{ route('back.route') }}"
                    class="inline-flex items-center px-4 py-2 bg-secondary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-secondary-700 focus:bg-secondary-700 active:bg-secondary-900 focus:outline-none focus:ring-2 focus:ring-secondary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <i class="fad fa-arrow-left mr-2"></i>
                    Back to List
                </a>
            </div>
        </div>

        <div class="px-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h2 class="text-lg font-medium text-gray-900">Section Title</h2>
                    <p class="mt-1 text-sm text-gray-600">Section description.</p>
                </div>

                <!-- Content goes here -->

            </div>
        </div>
    </div>
@endsection
```

---

## 🎯 **STEP 4: Component Color Standards**

### **🔘 BUTTONS**

```html
<!-- Primary Action Button -->
<button class="bg-primary-600 hover:bg-primary-700 text-white">Primary</button>

<!-- Secondary Action Button -->
<button class="bg-secondary-600 hover:bg-secondary-700 text-white">Secondary</button>

<!-- Success/Accent Button -->
<button class="bg-accent-600 hover:bg-accent-700 text-white">Success</button>

<!-- Danger/Delete Button -->
<button class="bg-danger-600 hover:bg-danger-700 text-white">Delete</button>

<!-- Cancel/White Button -->
<button class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-700">Cancel</button>
```

### **🏷️ STATUS INDICATORS**

```html
<!-- Active/Success Status -->
<span class="bg-accent-100 text-accent-800">Active</span>

<!-- Warning Status -->
<span class="bg-warning-100 text-warning-800">Warning</span>

<!-- Error Status -->
<span class="bg-danger-100 text-danger-800">Error</span>

<!-- Info Status -->
<span class="bg-secondary-100 text-secondary-800">Info</span>
```

### **📝 FORM ELEMENTS**

```html
<!-- Standard Input -->
<input class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">

<!-- Error Input -->
<input class="mt-1 block w-full rounded-md border-red-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm @error('field') border-red-300 @enderror">

<!-- Checkbox -->
<input type="checkbox" class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
```

---

## 🚫 **STEP 5: FORBIDDEN PATTERNS**

### **❌ NEVER USE THESE LAYOUTS**

```html
<!-- DON'T: Complex gradient backgrounds -->
<div class="bg-gradient-to-r from-blue-50 to-indigo-50">

<!-- DON'T: Multiple colored sections -->
<div class="bg-gradient-to-r from-green-50 to-teal-50">
<div class="bg-gradient-to-r from-purple-50 to-pink-50">

<!-- DON'T: Custom max-width containers -->
<div class="max-w-4xl mx-auto">

<!-- DON'T: Complex card shadows -->
<div class="shadow-xl border border-gray-200">
```

### **✅ USE THESE INSTEAD**

```html
<!-- DO: Simple white cards -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200">

<!-- DO: Standard container -->
<div class="px-6">

<!-- DO: Simple sections -->
<div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
```

---

## 📚 **STEP 6: Mandatory Reading List**

### **🔍 ALWAYS READ THESE FILES BEFORE CODING:**

1. **`FRESH_ADMIN_INDEX_TEMPLATE_GUIDE.md`** - Template structure guide
2. **`FRESH_VUEDATA_INSTALL.md`** - Vue.js integration guide  
3. **`FRESH_API_CONTROLER_INSTALL.md`** - API controller patterns
4. **`FRESH_FONTAWESOME.md`** - Icon usage guide
5. **`NUCLEO_MAP_COLOR_STANDARDS.md`** - Complete color guide
6. **`README.md`** - Project overview and rules

---

## 🔄 **STEP 7: Workflow Process**

### **📋 MANDATORY WORKFLOW FOR AI ASSISTANTS**

```
1. 🔍 READ: Check for FRESH_COLOR_STANDARD.md
2. 📖 STUDY: Read all color standards and guidelines
3. 🎨 PLAN: Choose appropriate colors from approved palette
4. 🏗️ BUILD: Use standard template structure
5. ✅ VERIFY: Check against forbidden patterns
6. 🔍 REVIEW: Ensure consistency with existing files
7. 📝 DOCUMENT: Update relevant documentation if needed
```

---

## ⚡ **QUICK REFERENCE CHECKLIST**

### **✅ Before submitting ANY Blade file:**

- [ ] Used semantic color classes (primary-, accent-, secondary-, danger-, warning-)
- [ ] Followed standard template structure
- [ ] Used approved button styles
- [ ] Used standard form element classes
- [ ] Avoided forbidden gradient patterns
- [ ] Used consistent spacing (px-6, py-4, gap-6)
- [ ] Included proper error handling styles
- [ ] Used standard card layout (white bg, gray-50 header)

---

## 🚨 **EMERGENCY RULES**

### **IF IN DOUBT:**

1. **STOP** - Don't generate code
2. **ASK** - Request clarification about color usage
3. **CHECK** - Review existing similar files
4. **COPY** - Use patterns from `admin/users/edit.blade.php` as template
5. **VERIFY** - Confirm with color standards

---

## 📞 **SUPPORT**

If you encounter any conflicts or uncertainties:

1. **Primary Reference**: `admin/users/edit.blade.php`
2. **Secondary Reference**: `admin/users/create.blade.php`
3. **Color Reference**: `NUCLEO_MAP_COLOR_STANDARDS.md`
4. **Layout Reference**: `FRESH_ADMIN_INDEX_TEMPLATE_GUIDE.md`

---

**⚠️ VIOLATION OF THESE RULES WILL RESULT IN CODE REJECTION**

**✅ FOLLOWING THESE RULES ENSURES CONSISTENT, PROFESSIONAL UI**

---

*Last Updated: {{ date('Y-m-d') }}*
*Version: 1.0 - Mandatory for ALL AI Development Tools* 