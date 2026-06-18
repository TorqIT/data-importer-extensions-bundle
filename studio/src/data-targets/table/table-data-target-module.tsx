import React from "react";
import { AbstractModule, container } from "@pimcore/studio-ui-bundle";
import { getDataTargetRegistry } from "../../common/consts/registries";
import { type DynamicTypeDataTargetRenderProps } from "../../common/types/DynamicTypeDataTargetRegistry";
import { TableDataTargetSettings } from "./table-data-target-settings";

export const TableDataTargetModule: AbstractModule = {
    onInit() {
        getDataTargetRegistry(container).registerDynamicType({
            id: "table",
            label: "Table",
            supportsType() {
                return true;
            },
            renderSettings(props: DynamicTypeDataTargetRenderProps) {
                return <TableDataTargetSettings {...props} />;
            },
        });
    },
};
