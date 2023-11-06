# BitBucket to Github Migration CLI

THis package was created for personal use and is still under development and prone to breaking changes.

## Getting started

### Installation

```bash
composer global require jascha030/bb2gh
```

## `mv` - Move a Repository from Bitbucket to Github

A Symfony Console command to move a repository from Bitbucket to Github.

### Usage

```bash
bb2gh mv [options] [directory]
```

#### Arguments

* directory (optional): The directory of the repository. Defaults to the current working directory.

#### Options

* `--organization`, `-o` The organization name.
* `--code-owner`, `-c` The owner of the repository.

#### Description

This command moves a repository from Bitbucket to Github It performs the following steps:

1.	It removes the origin remote from the local repository.

2.	It creates a new **Github** repository with the same name and description as the local repository.

3.	If the `--code-owner` option is provided, it creates a CODEOWNERS file in the `.github` directory of the repository with the specified owner.

#### Example

```bash
bb2gh  mv --organization=myorg --code-owner=myowner /path/to/repository
```

This example moves the repository located at `/path/to/repository` to Github under the organization myorg and assigns myowner as the code owner.

> Note: Ensure that you have gh (Github CLI) installed and available in your system's PATH.

> Note: This documentation assumes that you have Symfony Console set up in your project and the required dependencies are correctly installed.
