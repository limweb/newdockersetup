// Custom script to help Bun properly watch files on Windows
const { spawn } = require('child_process');
const path = require('path');
const fs = require('fs');

// Get the absolute path to the project directory
const projectDir = path.resolve(__dirname);
console.log(`Project directory: ${projectDir}`);

// Environment variables
const env = {
  ...process.env,
  MODE: 'dev',
  BUN_CONFIG_OVERRIDE: JSON.stringify({
    watch: {
      paths: [projectDir],
      extensions: ['.ts', '.js', '.json', '.toml'],
      ignore: ['node_modules/**/*'],
      include: ['**/*'],
    }
  })
};





// Run Bun with the correct working directory
const bunProcess = spawn('bun', ['run', '--watch', './src/index.ts'], {
  cwd: projectDir,
  env,
  stdio: 'inherit',
  shell: true
});

// Handle process events
bunProcess.on('error', (err) => {
  console.error(`Failed to start Bun: ${err}`);
});

// Handle exit
process.on('SIGINT', () => {
  bunProcess.kill('SIGINT');
  process.exit(0);
});
