git config --global user.name = "your_git_name"
git config --global user.email = "your_git@email.com"

ssh-keygen -t rsa -b 4096 -C "your_git@email.com"
ssh-keygen -t rsa -b 4096 -C "company" -f "id_rsa_company"
#now you havs id_rsa and id_rsa.pub 
# mv id_rsa* ./.ssh
# cd .ssh
# chmod 400 id_rsa*
# start the ssh-agent in the background
# eval $(ssh-agent -s)

# ssh-add ~/.ssh/id_rsa
# ssh-add ./id_rsa
# add key to github before git command
# cat .ssh/id_rsa.pub | ssh username@ip 'cat >> .ssh/authorized_keys'
# ssh-copy-id remote_username@server_ip_address
# git clone git@github.com:your_git/your_repo.git