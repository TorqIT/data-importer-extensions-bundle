import { AbstractModule, container } from "@pimcore/studio-ui-bundle";
import { getTransformerRegistry } from "../../common/consts/registries";

export const AsCountryCodeTransformerModule: AbstractModule = {
    onInit() {
        getTransformerRegistry(container).registerDynamicType({
            id: "asCountryCode",
            label: "As Country Code",
            group: "dataTypes",
            renderSettings() {
                return null;
            },
        });
    },
};
