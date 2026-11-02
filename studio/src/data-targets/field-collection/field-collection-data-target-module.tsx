import { AbstractModule, container } from "@pimcore/studio-ui-bundle";
import { getDataTargetRegistry } from "../../common/consts/registries";

export const FieldCollectionDataTargetModule: AbstractModule = {
    onInit() {
        getDataTargetRegistry(container).registerDynamicType({
            id: "fieldCollection",
            label: "Field Collection",
            supportsType() {
                return true;
            },
            renderSettings() {
                return null;
            },
        });
    },
};
