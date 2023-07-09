import os
from copy import deepcopy

DOCKER_PATH_YML = 'docker/docker-compose.yaml'
APP_ENV = os.environ.get("envVars")  # get epxorted app env

with open(DOCKER_PATH_YML, "r") as f:  # to get all the lines into a list
    contents = f.readlines()


def checkEnv(contents):  # function to check if environment filed is present in docker-compose.yml
    if (not ("    environment:\n" in contents)):
        print("ENVIRONMENT FIELD NOT FOUND IN DOCKER-COMPOSE FILE")
    else:
        print("ENVIRONMENT FIELD FOUND")


def getEnvIndex():  # gets the index for environment filed
    print(contents)
    index = contents.index('    environment:\n')

    return index


def addEnv():  # function that writes environment variables from the system to docker-compose file
    with open(APP_ENV, 'r') as file:
        content = file.read()
        with open('.env', 'w+') as f:
            f.write(content)
        f.close()

        envVars = ''
        for line in file:
            line = line.strip() #removes all blank spaces between lines
            if line:
                #REMOVE COMMENTS
                if(line.startswith('#')):
                    continue
                line=line.strip()
                #REMOVE COMMENTS
                print(line)
                variable = line.split('=')
                first = variable[0]
                value = f'      {first}: ' + '"${' + first + '}"\n'
                envVars += value

        keyword = '    environment:\n'
        with open(DOCKER_PATH_YML, "r+") as f:
            lines = f.readlines()
            for index, line in enumerate(lines):
                is_environment = (line == keyword)
                if is_environment:
                    lines[index] = f"{keyword}{envVars}"
        writeToFile(lines)


def writeToFile(data):  # function that writes to file # noqa
    with open(DOCKER_PATH_YML, "w") as f:
        f.writelines(data)


if __name__ == "__main__":  # main function

    if (os.path.exists(DOCKER_PATH_YML) == True):  # if docker compose file exists
        checkEnv(contents)
        addEnv()
    else:
        print("DOCKER COMPSOE FILE NOT FOUND....")
