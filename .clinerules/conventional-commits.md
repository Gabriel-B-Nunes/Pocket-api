# Conventional Commits

## Summary
The Conventional Commits specification is a lightweight convention on top of commit messages. It provides an easy set of rules for creating an explicit commit history; which makes it easier to write automated tools on top of.

## Strutural Elements
1. fix: a commit of the type fix patches a bug in your codebase (this correlates with PATCH in Semantic Versioning).
2. feat: a commit of the type feat introduces a new feature to the codebase (this correlates with MINOR in Semantic Versioning).
3. BREAKING CHANGE: a commit that has a footer BREAKING CHANGE:, or appends a ! after the type/scope, introduces a breaking API change (correlating with MAJOR in Semantic Versioning). A BREAKING CHANGE can be part of commits of any type.

types other than fix: and feat: are allowed. Recommends build:, chore:, ci:, docs:, style:, refactor:, perf:, test:, and others.

## Specification
1. The key words “MUST”, “MUST NOT”, “REQUIRED”, “SHALL”, “SHALL NOT”, “SHOULD”, “SHOULD NOT”, “RECOMMENDED”, “MAY”, and “OPTIONAL” in this document are to be interpreted as described in RFC 2119.
2. Commits MUST be prefixed with a type, which consists of a noun, feat, fix, etc., followed by the OPTIONAL scope, OPTIONAL !, and REQUIRED terminal colon and space.
3. The type feat MUST be used when a commit adds a new feature to your application or library.
4. The type fix MUST be used when a commit represents a bug fix for your application.
5. A scope MAY be provided after a type. A scope MUST consist of a noun describing a section of the codebase surrounded by parenthesis, e.g., fix(parser):
6. A description MUST immediately follow the colon and space after the type/scope prefix. The description is a short summary of the code changes, e.g., fix: array parsing issue when multiple spaces were contained in string.
7. A longer commit body MAY be provided after the short description, providing additional contextual information about the code changes. The body MUST begin one blank line after the description.
8. A commit body is free-form and MAY consist of any number of newline separated paragraphs.
9. One or more footers MAY be provided one blank line after the body. Each footer MUST consist of a word token, followed by either a :<space> or <space># separator, followed by a string value (this is inspired by the git trailer convention).
10. A footer’s token MUST use - in place of whitespace characters, e.g., Acked-by (this helps differentiate the footer section from a multi-paragraph body). An exception is made for BREAKING CHANGE, which MAY also be used as a token.
11. A footer’s value MAY contain spaces and newlines, and parsing MUST terminate when the next valid footer token/separator pair is observed.
12. Breaking changes MUST be indicated in the type/scope prefix of a commit, or as an entry in the footer.
13. If included as a footer, a breaking change MUST consist of the uppercase text BREAKING CHANGE, followed by a colon, space, and description, e.g., BREAKING CHANGE: environment variables now take precedence over config files.
14. If included in the type/scope prefix, breaking changes MUST be indicated by a ! immediately before the :. If ! is used, BREAKING CHANGE: MAY be omitted from the footer section, and the commit description SHALL be used to describe the breaking change.
15. Types other than feat and fix MAY be used in your commit messages, e.g., docs: update ref docs.
16. The units of information that make up Conventional Commits MUST NOT be treated as case-sensitive by implementors, with the exception of BREAKING CHANGE which MUST be uppercase.
BREAKING-CHANGE MUST be synonymous with BREAKING CHANGE, when used as a token in a footer.