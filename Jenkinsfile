pipeline {
  agent {
    docker {
      image 'composer:2.4.1'
    }

  }
  stages {
    stage('composer install') {
      steps {
        sh 'composer install --no-scripts --no-autoloader --ignore-platform-reqs'
      }
    }

  }
}