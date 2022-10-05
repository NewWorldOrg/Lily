pipeline {
  agent none
  stages {
    stage('setup environment') {
      parallel {
        stage('composer install') {
          agent {
            docker {
              image 'composer:2.4.1'
            }
          }
          steps {
            sh 'composer install --no-scripts --no-autoloader --ignore-platform-reqs'
          }
        }

        stage('init database environment') {
          agent {
            docker {
              image 'mysql:5.7'
              args '-e MYSQL_ROOT_PASSWORD=root -e MYSQL_DATABASE=testing --hostname=db '
            }
          }
          steps {
            sh 'service mysql start'
          }
        }
      }
    }
    stage('migration') {
      steps {
        sh 'php artisan migrate --env=testing'
      }
    }
    stage('php unit') {
      steps {
        sh 'vendor/bin/phpunit --testdox --colors=always'
      }
    }
  }
}
