#!/bin/bash

STAGE=${1:?STAGE is required}

if [ "$STAGE" = "dev" ]; then
  STAGE=$STAGE docker compose -p cocohairsignature_com -f docker-compose.yml -f docker-compose-override.yml up -d --build
elif [ "$STAGE" = "prod" ]; then
  STAGE=$STAGE docker compose -p cocohairsignature_com -f docker-compose.yml up -d --build
else
  echo "Usage: $0 [dev|prod]"
  exit 1
fi