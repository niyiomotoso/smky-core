#!/bin/bash

# any future command that fails will exit the script
set -e

# Lets write the public key of our aws instance
eval $(ssh-agent -s)
echo "$PROD_PRIVATE_KEY" | tr -d '\r' | ssh-add - > /dev/null

# ** Alternative approach
# echo -e "$PRIVATE_KEY" > /root/.ssh/id_rsa
# chmod 600 /root/.ssh/id_rsa
# ** End of alternative approach
# disable the host key checking.
./deploy/prod/disableHostKeyChecking.sh
# we have already setup the PROD_SERVERS in our gitlab settings which is a
# comma seperated values of ip addresses.
PROD_DEPLOY_SERVERS=$PROD_SERVERS

# lets split this string and convert this into array
# In UNIX, we can use this commond to do this
# ${string//substring/replacement}
# our substring is "," and we replace it with nothing.
ALL_SERVERS=(${PROD_DEPLOY_SERVERS//,/ })
echo "ALL_SERVERS ${ALL_SERVERS}"

# Lets iterate over this array and ssh into each EC2 instance
# Once inside the server, run updateAndRestart.sh
for server in "${ALL_SERVERS[@]}"
do
  echo "deploying to ${server}"
  ssh ubuntu@${server} 'bash' < ./deploy/prod/updateServerCodebase.sh
done
