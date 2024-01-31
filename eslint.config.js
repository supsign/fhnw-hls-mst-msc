import eslint from '@supsign/eslint-config';

// import autoImport from './.eslintrc-auto-import.json' assert { type: 'json' };

export default eslint({
  typescript: {
    tsconfigPath: 'tsconfig.json'
  }
  /* languageOptions: {
    globals: autoImport.globals
  } */
}, {
  rules: {
    'style/space-infix-ops': ['error', { int32Hint: true }],
    'vue/attributes-order': ['error', {
      alphabetical: true,
      order: [
        'DEFINITION',
        'LIST_RENDERING',
        'CONDITIONALS',
        'RENDER_MODIFIERS',
        'GLOBAL',
        ['UNIQUE', 'SLOT'],
        'TWO_WAY_BINDING',
        'OTHER_DIRECTIVES',
        'OTHER_ATTR',
        'EVENTS',
        'CONTENT'
      ]
    }]
  }
});
