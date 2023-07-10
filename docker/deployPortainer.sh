#!/bin/bash

function deployment_failed_message() {
  echo -e "\e[31m  FAILED: ${1}.\e[0m"
  exit 1
}
function deployment_warning_message () {
  echo -e "\e[33m  WARNING: ${1}.\e[0m"
}
function deployment_info_message () {
  echo ""
  echo -e "\e[32m  INFO: ${1}.\e[0m"
}

function check_exit_code () {
  if [[ $? != 0 ]]; then
    deployment_failed_message "$@"
    exit 1
  fi
}

function check_null () {
  if [[ -z "$@" ]]; then
    deployment_failed_message "Variable <  '"$@"'  > is null"
  fi
}

function prepare_deploy_envvars (){
  while read LINE || [ -n "$LINE" ]; do
    if [[ ${LINE} != "" && ${LINE} != '#'* ]]; then
      lineVar=$(echo $LINE | envsubst)
      export ${lineVar}
    fi
  done < ${envVars}
  check_exit_code "function prepare_deploy_envvars failed"
}

function deploy_stack (){
  deployment_info_message "Prepare environment variables file (print some of them) ..."
  composeFile="$PWD/docker/docker-compose.yaml"
  prepareENV="$PWD/docker/prepareEnv.py"
  if [[ -f $composeFile ]]; then
    echo " File found - $composeFile"
#    python3 $prepareENV
    cat $composeFile
  else
    deployment_failed_message " File $composeFile does not found"
  fi
  if [[ -f ${envVars} ]]; then
    echo " Found envVars CI  variables file, creting .env file ..."
    echo "DOCKER_IMAGE_TAG=$DOCKER_IMAGE_TAG" >> ${envVars}
    cat ${envVars} | grep -v "PASS" | grep "DOCKER_IMAGE_TAG\|PORTAINER\|ENVIRONMENT"
    prepare_deploy_envvars
    # nc -zv  ${DOCKER_APP_DOMAIN} ${DOCKER_APP_EXP_PORT} -w 5
    env | grep -v "PASS" | grep "DOCKER\|PORTAINER\|ENVIRONMENT"
    psu stack deploy
    check_exit_code "function deploy_stack failed"
  elif [[ -f .env ]]; then
      echo " Found file .env"
      cat .env | grep -v "PASS" | grep "DOCKER\|PORTAINER\|ENVIRONMENT"
      docker run -t --rm  --env-file $PWD/.env  -v $PWD/.env:/env \
        -v $composeFile:/docker-compose.yaml git2.devebs.net:4567/templates/images/tools:docker-git-portainer-stack-utils-01 \
        psu stack deploy
      check_exit_code "function deploy_stack failed local"
  else
    deployment_failed_message " File .env or envVars CI  variables file does not found"
  fi
  check_exit_code "function prepare_environment_variables failed pipeline"
}

function check_deploy (){
  deployment_info_message " Checking deployment nc -zv  ${DOCKER_APP_DOMAIN} ${DOCKER_APP_EXP_PORT} -w 5 ..."
  for i in {1..10}; do
    if [ ! -z ${DOCKER_APP_DOMAIN} ]; then
      echo -n " Check nc -zv  ${DOCKER_APP_DOMAIN} ${DOCKER_APP_EXP_PORT} -w 5 "
    #   nc -zv  ${DOCKER_APP_DOMAIN} ${DOCKER_APP_EXP_PORT} -w 5
      if [[ $? != 0 ]]; then
        echo $?; sleep 5
      else
        echo " Deployment Success "
        exit 0
      fi
    fi
  done
  sleep 30
#   nc -zv  ${DOCKER_APP_DOMAIN} ${DOCKER_APP_EXP_PORT} -w 5
  check_exit_code " Deployment FAILED "
}

deploy_stack
check_deploy
