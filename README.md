# Symfony CQRS/ES with ApiPlatform, PostgreSQL and MongoDB with a DDD approach
This project is meant to get a better understanding of CQRS and ES. The goal is to have a EventSourced application,
with Symfony components only. While setting it up, it should also provide the insights on how CQRS and ES is used and applied
within the application. EventSourcing packages for PHP already exist, this is not an intent to either replace those, or offer
a new one.

---

## Table of content

1. [Introduction](#the-todo-list)
2. [Choosing the tools](#choosing-the-tools)
3. [Setting up the project](#setting-up-the-project)
4. [Event sourcing solutions for PHP](#event-sourcing-solutions-for-php)

## The Todo List
Even though a Todo List might seem as a very simple and basic goal, it still covers a lot of key aspects that are of importance
when creating an application. It gives all the (conventional) CRUD actions within a small and compact application. For this
application we need to:

- Create new lists
- Update created lists
- Delete the lists
- Preform all provided actions through events
- Generate read models based on said events
- Replay events to create the read models again from scratch
- Upcast events
- Use projectors to notify if a list is about to expire, or has expired
- Use projectors to notify if a list is completed

## Choosing the tools
In order to build this, I have chosen some tools that I am either familiar with, or sound like the logical solution.

The chosen approach is:

- Framework: [Symfony](https://symfony.com/)
- Persisting events: [PostgreSQL](https://www.postgresql.org/)
- Persisting read models: [MongoDB](https://www.mongodb.com/)
- Transport: [RabbitMQ](https://www.rabbitmq.com/)
- Local development: [Docker](https://www.docker.com/)

I chose Symfony as the framework, since Symfony offers a variety of components, that help in setting everything up.
Since the components used are all part of Symfony itself, it will make it reliable, and trustworthy of maintenance.
Due to its minimalistic approach, Symfony offers a lot of room to create the application in the desired setup.

In case of storing (or persisting) the events, I choose for PostgreSQL. I did consider using [EventStore (now known as KurrentDB)](https://github.com/kurrent-io/KurrentDB),
but it would involve learning that as well apart from just the ES part. To keep it simple, yet have a proper database,
I chose PostgreSQL. There are undoubtedly plenty of extensions to make PostgreSQL work as smooth as possible with storing
and retrieving events. 

Since read models are basically a _current_ state of a model, or are used to easily serve a view, I chose for MongoDB.
The fact that it's a non-relational database (NRDB), it helps with not needing to write migrations every time I make
changes to the read model. I did doubt between MongoDB and [ElasticSearch](https://www.elastic.co/), but ended up choosing MongoDB.
The main reason was that MongoDB handles lots of write and update statements better, and less resource-intensive than
ElasticSearch. Since we're dealing with read models that are either written, or updated, MongoDB seemed the right choice,

For the message transport, I chose RabbitMQ, mostly because I am familiar with it. In a pas project I did use [ActiveMQ](https://activemq.apache.org/),
however, that was just a lot of hassle to set up with a PHP project. I might revisit the decision when I decide to dive into [Kafka](https://kafka.apache.org/).
Just to see which of the two would be easier to implement and offers more value than the other.

For local development the goto in my case is Docker. It makes it so that I can set everything up without too much hassle.
I am planning on looking into [Podman](https://podman.io/) since its also open source, and offers containerization of services,
_plus_ if I understand correctly, it spins everything up without needing root. Also might iterate over this project in the future
by implementing [Kubernetes](https://kubernetes.io/).

## Setting up the project
To run the project in its current state, you need [docker-compose](https://docs.docker.com/compose/).
The project uses [Traefik](https://traefik.io/traefik) to act a local proxy, and direct the correct request to the correct container.
Even though in its current state we only make use of one API, Traefik does offer a nice solution to have multiple APIs run within the
same setup. Apart from that it also offers a easy way of handling TLS with self generated certificates.

In order to generate said certificates, I made use of [mkcert](https://github.com/FiloSottile/mkcert). It's an easy and
hassle-free way of generating some certificates.

### Generating the certificates (optional)
The current `compose.yaml` assumes that `https` will be used. To provide Traefik with the right certificates do the following:

- In your terminal, navigate to the `./docker/traefik/cert` directory
```shell
cd docker/traefik/cert
```
- Create the certificates by running:
```shell
mkcert "*.todolist.test"
```
_This will generate a wildcard certificate that can be used for `todolist.test`, and its subdomains._

- Add the `api.todolist.test` to your `/etc/hosts`
```shell
echo '127.0.0.1 api.todolist.test' | sudo tee -a /etc/hosts
```
_This project happens to use a `api` subdomain. If you were to need another subdomain, for a additional service for example, make sure
to add that subdomain to your `/etc/hosts` as well_

### Spinning up the containers
Now that the certificates are in place, we need to spin up the development environment.

- Make sure you have a `.env.dev` file
```shell
cp .env .env.dev
```
- Spin up the containers
```shell
docker compose up -d
```
### Initiating the EventStore and ReadModels
At this point, no tables have been created in the databases. To do this run the following commands:

- Enter the php container
```shell
docker compose exec todolist_php sh
```
- Run the command to create the event store
```shell
bin/console app:store:create
```
- Run the command to create the read models collections
```shell
bin/console app:read:create
```
_This command will create three different collections: detail-view, notified, overview. Each serving its own purpose
in either displaying a view, or keeping track of send notification_

### Start the consumers
- In order to handle all the events, commands and projectors, we need to start our workers to consume messages from the queue
```shell
bin/console messenger:consume async -vv
```

### The ApiPlatform documentation page
Since the application uses ApiPlatform, you can now navigate to the [documentation page](https://api.todolist.test/api).
You can also visit the [RabbitMQ dashboard](http://todolist.test:15672/). If you were to need some information regarding
Traefik, you can visit the [Traefik dashboard](http://todolist.test:8080/dashboard/)

## Event Sourcing solutions for PHP

If you are looking for a package that offers ES you can look at:

* [Prooph](https://github.com/prooph)
* [Broadway](https://github.com/broadway)
* [EventSaucePHP](https://github.com/EventSaucePHP)

_Sidenote: to my understanding, EventSaucePHP is best maintained at the moment of writing this. Even though
my solution **is** heavily inspired by Broadway, the Broadway package is not maintained for modern php versions. The
Prooph package had components that were marked as deprecated, so unsure as to what their current status is in the whole._

---
#### ***My approach is heavily inspired by [Broadway](https://github.com/broadway/broadway).**

