'use strict'

const path = require('node:path')
const AutoLoad = require('@fastify/autoload')

// Load environment variables
require('dotenv').config()

// Server configuration
const options = {
  port: parseInt(process.env.PORT || '3000', 10),
  host: process.env.HOST || '0.0.0.0',
  logger: true
}

module.exports = async function (fastify, opts) {
  // Add health check endpoint
  fastify.get('/health', async (request, reply) => {
    return { 
      status: 'ok',
      timestamp: new Date().toISOString()
    };
  });

  // Do not touch the following lines

  // This loads all plugins defined in plugins
  // those should be support plugins that are reused
  // through your application
  fastify.register(AutoLoad, {
    dir: path.join(__dirname, 'plugins'),
    options: Object.assign({}, opts)
  })

  // This loads all plugins defined in routes
  // define your routes in one of these
  fastify.register(AutoLoad, {
    dir: path.join(__dirname, 'routes'),
    options: Object.assign({}, opts)
  })
}

module.exports.options = options
